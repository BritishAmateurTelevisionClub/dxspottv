<?php
session_start();
include('../spot_login.php');

$output = array();
$i=1;
$user_result = mysqli_query($dbc, "SELECT id,callsign,name,lat,lon,known FROM users;") or die(mysqli_error($dbc));
while($user_row = mysqli_fetch_array($user_result))
{
	// Get session activity data
	$user_id = $user_row['id'];
	$session_result = mysqli_query($dbc, "SELECT activity FROM sessions WHERE user_id='{$user_id}';") or die(mysqli_error($dbc));
	$session_row = mysqli_fetch_array($session_result);
	$minutes_diff = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%i'); // Used for icons
	$months_diff = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%m');
	$hours_diff = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%H');
	$days_diff = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%d');
	$output[$i] = array();
	$output[$i]['callsign'] = $user_row['callsign'];
	$output[$i]['latitude'] = $user_row['lat'];
	$output[$i]['longitude'] = $user_row['lon'];
	$output[$i]['longitude'] = $user_row['lon'];
	$output[$i]['known'] = $user_row['known'];
	$listen_result = mysqli_query($dbc, "SELECT * FROM listening WHERE user_id='{$user_id}';") or die(mysqli_error($dbc));
	$listen_row = mysqli_fetch_array($listen_result);
	$output[$i]['is70cm'] = $listen_row['70cm_listen'];
	if($output[$i]['is70cm']==1) {
		$output[$i]['70cmFreq'] = $listen_row['70cm_freq'];
	}
	$output[$i]['is23cm'] = $listen_row['23cm_listen'];
	if($output[$i]['is23cm']==1) {
		$output[$i]['23cmFreq'] = $listen_row['23cm_freq'];
	}
	$output[$i]['is13cm'] = $listen_row['13cm_listen'];
	if($output[$i]['is13cm']==1) {
		$output[$i]['13cmFreq'] = $listen_row['13cm_freq'];
	}
	$output[$i]['minutes_active'] = $minutes_diff;
	$output[$i]['hours_active'] = $hours_diff;
	$output[$i]['days_active'] = $days_diff;
	$output[$i]['months_active'] = $months_diff;
	$i++;
}

$json_output = json_encode($output);
echo $json_output;
mysql_end($dbc);
?>
