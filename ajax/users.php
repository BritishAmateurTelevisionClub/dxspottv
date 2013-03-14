<?php
session_start();
include('../spot_login.php');

$output = array();
$i=1;
$session_result = mysqli_query($dbc, "SELECT user_id,activity FROM sessions;") or die(mysqli_error($dbc));
while($session_row = mysqli_fetch_array($session_result))
{
	$now = date_create();
	$activity_diff = date_create();
	date_sub($threeh, date_interval_create_from_date_string('3 hours'));
	$threeh = date_create();
	date_sub($threeh, date_interval_create_from_date_string('3 hours'));
	$twelveh = date_create();
	date_sub($twelveh, date_interval_create_from_date_string('12 hours'));
	if(strtotime($session_row['activity'])>$threeh) {
		$user_id = $session_row['user_id'];
		$user_result = mysqli_query($dbc, "SELECT callsign,lat,lon FROM users WHERE id='{$user_id}';") or die(mysqli_error($dbc));
		$user_row = mysqli_fetch_array($user_result);
		$output[$i] = array();
		$output[$i]['callsign'] = $user_row['callsign'];
		$output[$i]['latitude'] = $user_row['lat'];
		$output[$i]['longitude'] = $user_row['lon'];
		date_sub($activity_diff,$session_row['activity']);
		$output[$i]['active'] = date_format($activity_diff, 'h');
	}
}

$json_output = json_encode($output);
echo $json_output;
mysql_end($dbc);
?>
