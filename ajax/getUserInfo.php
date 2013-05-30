<?php
session_start();
$output = array();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
if($got_cookies) {
	require_once('spot_login.php');
	$sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));  
	if(mysqli_num_rows ($sessions_result)==0) { // session doesn't exist on server
		$output['error'] = 'No user session';
	} else {
		$target_row = mysqli_fetch_array($sessions_result);
		if ($_COOKIE["session_key"]==$target_row["session_id"]) {
			// Session matches, so is logged in!
			$output = array();
			$user_statement = $dbc->prepare("SELECT callsign,lat,lon,locator,station_desc,website FROM users WHERE id=?;");
			$user_statement->bind_param('i', $_COOKIE["user_id"]);
			$user_statement->execute();
			$user_statement->bind_result($output['callsign'], $output['lat'], $output['lon'], $output['locator'], $output['description'], $output['website']);
			$user_statement->fetch();
		} else {
			$output['error'] = 'No matching session';
		}
	}
	mysql_end($dbc);
} else { // Not got cookies
	$output['error'] = 'No cookies';
}
print json_encode($output);
?>
