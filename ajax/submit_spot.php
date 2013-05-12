<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = (isset($_REQUEST["freq"]) && isset($_REQUEST["mode"]));
if($got_cookies && $got_variables) {
include_once("login_functions.php");
include('spot_login.php');

$sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));  
if(mysqli_num_rows ($sessions_result)==0) { // session doesn't exist on server
	print 'Session not found.';
} else {
	$target_row = mysqli_fetch_array($sessions_result);
	if ($_COOKIE["session_key"]==$target_row["session_id"]) {
		// Session matches, so is logged in!
		$user_id = $_COOKIE["user_id"];
		$freq = mysqli_real_escape_string($dbc, $_REQUEST["freq"]);
		$mode_id = mysqli_real_escape_string($dbc, $_REQUEST["mode"]);
		$comments = mysqli_real_escape_string($dbc, $_REQUEST["comments"]);
		$r_callsign = mysqli_real_escape_string($dbc, $_REQUEST["r_callsign"]);
		$r_locator = mysqli_real_escape_string($dbc, $_REQUEST["r_locator"]);
		$r_lat = mysqli_real_escape_string($dbc, $_REQUEST["r_lat"]);
		$r_lon = mysqli_real_escape_string($dbc, $_REQUEST["r_lon"]);
		
		$check_existing_user = mysqli_query($dbc, "SELECT id FROM users WHERE callsign='{$r_callsign}';") or die(mysqli_error($dbc));
		if(mysqli_num_rows ($check_existing_user)==0) { // End User doesn't exist, so add them
			print 'Adding new unknown callsign.';
			// Insert into users tables
			mysqli_query($dbc, "INSERT into users (callsign, locator, lat, lon, known) VALUES ('{$r_callsign}', '{$r_locator}', '{$r_lat}', '{$r_lon}', '0');");
			// Grab allocated user_id
			$check_existing_user = mysqli_query($dbc, "SELECT id FROM users WHERE callsign='{$r_callsign}';") or die(mysqli_error($dbc));
		}
		$check_existing_user_row = mysqli_fetch_array($check_existing_user);
		$r_userid = $check_existing_user_row['id'];
		$add_spot_query = "INSERT into spots (mode_id, frequency, primary_id, secondary_id, comments) VALUES ('{$mode_id}', '{$freq}', '{$user_id}', '{$r_userid}', '{$comments}');";
		mysqli_query($dbc, $add_spot_query) or die(mysqli_error($dbc));
		// Add activity for remote callsign
		updateRemoteUserActivity($user_id);
		print 'Spot Added!';
	} else {
        print 'Session doesnt match.';
	}
}
} else { // Not got cookies or variables
print 'Access Denied.';
}
function updateRemoteUserActivity($user_id) {
	$existing_session_result = mysqli_query($dbc, "SELECT user_id from sessions WHERE user_id='{$user_id}';") or die(mysqli_error($dbc));
	if(mysqli_num_rows ($existing_session_result)==0) { // No session exists
		mysqli_query($dbc, "INSERT into sessions (user_id) VALUES ('{$user_id}');") or die(mysqli_error($dbc));
	} else { // Session exists, update timestamp
		mysqli_query($dbc, "UPDATE sessions SET activity=NOW() WHERE user_id='{$user_id}';") or die(mysqli_error($dbc));
	}
}
?>
