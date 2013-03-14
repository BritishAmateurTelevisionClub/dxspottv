<?php
session_start();
include('../spot_login.php');

$output = array();
$i=1;
$session_result = mysqli_query($dbc, "SELECT user_id,activity FROM sessions;") or die(mysqli_error($dbc));
while($session_row = mysqli_fetch_array($session_result))
{
	$activity_diff = date_format(date_sub(date_create(), strtotime($session_row['activity'])), 'h');
	$user_id = $session_row['user_id'];
	$user_result = mysqli_query($dbc, "SELECT callsign,lat,lon FROM users WHERE id='{$user_id}';") or die(mysqli_error($dbc));
	$user_row = mysqli_fetch_array($user_result);
	$output[$i] = array();
	$output[$i]['callsign'] = $user_row['callsign'];
	$output[$i]['latitude'] = $user_row['lat'];
	$output[$i]['longitude'] = $user_row['lon'];
	$output[$i]['active'] = $activity_diff;
}

$json_output = json_encode($output);
echo $json_output;
mysql_end($dbc);
?>
