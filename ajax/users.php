<?php
session_start();
include('../spot_login.php');

$output = array();
$i=1;
$session_result = mysqli_query($dbc, "SELECT user_id,activity FROM sessions;") or die(mysqli_error($dbc));
while($session_row = mysqli_fetch_array($session_result))
{
	$months_diff = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%m');
	$hours_diff = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%H');
	$days_diff = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%d');
	$user_id = $session_row['user_id'];
	$user_result = mysqli_query($dbc, "SELECT callsign,lat,lon FROM users WHERE id='{$user_id}';") or die(mysqli_error($dbc));
	$user_row = mysqli_fetch_array($user_result);
	$output[$i] = array();
	$output[$i]['callsign'] = $user_row['callsign'];
	$output[$i]['latitude'] = $user_row['lat'];
	$output[$i]['longitude'] = $user_row['lon'];
	$output[$i]['hours_active'] = $hours_diff;
	$output[$i]['days_active'] = $days_diff;
	$output[$i]['months_active'] = $months_diff;
}

$json_output = json_encode($output);
echo $json_output;
mysql_end($dbc);
?>
