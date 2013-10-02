<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
if($got_cookies) {
	require_once('dxspottv_login.php');
	$output = array();
	$user_statement = $dbc->prepare("SELECT callsign,lat,lon,locator,station_desc,website,radio_active FROM users WHERE id=?;");
	$user_statement->bind_param('i', $_COOKIE["user_id"]);
	$user_statement->execute();
	$user_statement->bind_result($output['callsign'], $output['lat'], $output['lon'], $output['locator'], $output['description'], $output['website'], $output['radio_active']);
	$user_statement->fetch();
	$user_statement->close();
} else { // Not got cookies
	$output['error'] = 'No cookies';
}
print json_encode($output);
?>
