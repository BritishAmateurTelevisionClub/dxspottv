<?php
session_start();
$output = array();
$output['success'] = 0;
$output['error'] = 0;
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = (isset($_REQUEST["band_id"]) && isset($_REQUEST["mode"]));
if($got_cookies && $got_variables) {
require_once("dxspottv_login_functions.php");
require_once('dxspottv_login.php');

$sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));  
if(mysqli_num_rows ($sessions_result)==0) { // session doesn't exist on server
	$output['success'] = 0;
	$output['error'] = 2;
} else {
	while($target_row = mysqli_fetch_array($sessions_result)) { // find a matching session
		if ($_COOKIE["session_key"]==$target_row["session_id"]) {
			// Session matches, so is logged in!
			$user_id = $_COOKIE["user_id"];
			$band_id = mysqli_real_escape_string($dbc, $_REQUEST["band_id"]);
			$mode_id = mysqli_real_escape_string($dbc, $_REQUEST["mode"]);
			$comments = mysqli_real_escape_string($dbc, htmlentities($_REQUEST["comments"]));
			$r_callsign = mysqli_real_escape_string($dbc, htmlentities(strtoupper($_REQUEST["r_callsign"])));
			$r_locator = mysqli_real_escape_string($dbc, htmlentities(strtoupper($_REQUEST["r_locator"])));
			$r_lat = mysqli_real_escape_string($dbc, $_REQUEST["r_lat"]);
			$r_lon = mysqli_real_escape_string($dbc, $_REQUEST["r_lon"]);
		
			$check_existing_user = mysqli_query($dbc, "SELECT id FROM users WHERE callsign='{$r_callsign}';") or die(mysqli_error($dbc));
			$check_existing_repeater = mysqli_query($dbc, "SELECT id FROM all_repeaters WHERE callsign='{$r_callsign}';") or die(mysqli_error($dbc));
			if(mysqli_num_rows ($check_existing_user)!=0) { // End user exists
		
				$check_existing_user_row = mysqli_fetch_array($check_existing_user);
				$r_userid = $check_existing_user_row['id'];
				if($r_userid == $user_id) {
					$output['success'] = 0;
					$output['error'] = 3;
					die(json_encode($output));
				}
				$add_spot_query = "INSERT into spots (mode_id, band_id, primary_id, secondary_id, comments) VALUES ('{$mode_id}', '{$band_id}', '{$user_id}', '{$r_userid}', '{$comments}');";
				mysqli_query($dbc, $add_spot_query) or die(mysqli_error($dbc));
			
			} else if(mysqli_num_rows ($check_existing_repeater)!=0) { // Is a repeater
			
				$check_existing_repeater_row = mysqli_fetch_array($check_existing_repeater);
				$r_userid = $check_existing_repeater_row['id'];
				$add_spot_query = "INSERT into spots (mode_id, band_id, primary_id, secondary_id, secondary_isrepeater, comments) VALUES ('{$mode_id}', '{$band_id}', '{$user_id}', '{$r_userid}', '1', '{$comments}');";
				mysqli_query($dbc, $add_spot_query) or die(mysqli_error($dbc));
			
			} else { // New unknown user
				// Insert into users tables
				mysqli_query($dbc, "INSERT into users (callsign, locator, lat, lon, known) VALUES ('{$r_callsign}', '{$r_locator}', '{$r_lat}', '{$r_lon}', '0');");
				// Grab allocated user_id
				$check_existing_user = mysqli_query($dbc, "SELECT id FROM users WHERE callsign='{$r_callsign}';") or die(mysqli_error($dbc));
				$check_existing_user_row = mysqli_fetch_array($check_existing_user);
				$r_userid = $check_existing_user_row['id'];
				$add_spot_query = "INSERT into spots (mode_id, band_id, primary_id, secondary_id, comments) VALUES ('{$mode_id}', '{$band_id}', '{$user_id}', '{$r_userid}', '{$comments}');";
				mysqli_query($dbc, $add_spot_query) or die(mysqli_error($dbc));
			}
			$output['success'] = 1;
			$output['error'] = 0;
		}
	}
}
} else { // Not got cookies or variables
	$output['success'] = 0;
	$output['error'] = 1;
}
print json_encode($output);
?>
