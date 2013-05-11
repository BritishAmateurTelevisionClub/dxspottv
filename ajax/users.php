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
	$output[$i]['longitude'] = $user_row['lon'];
	$listen_result = mysqli_query($dbc, "SELECT * FROM listening WHERE user_id='{$user_id}';") or die(mysqli_error($dbc));
	$listen_row = mysqli_fetch_array($listen_result);
	$output[$i]['is70cm'] = $user_row['70cm_listen'];
	if($output[$i]['is70cm']==1) {
		$output[$i]['70cmFreq'] = $user_row['70cm_freq'];
	}
	$output[$i]['is23cm'] = $user_row['23cm_listen'];
	if($output[$i]['is23cm']==1) {
		$output[$i]['23cmFreq'] = $user_row['23cm_freq'];
	}
	$output[$i]['is13cm'] = $user_row['13cm_listen'];
	if($output[$i]['is13cm']==1) {
		$output[$i]['13cmFreq'] = $user_row['13cm_freq'];
	}
	$output[$i]['hours_active'] = $hours_diff;
	$output[$i]['days_active'] = $days_diff;
	$output[$i]['months_active'] = $months_diff;
	$i++;
}

$json_output = json_encode($output);
echo $json_output;
mysql_end($dbc);
?>
