<?php
session_start();
include('../spot_login.php');

$output = array();
$i=1;
$user_result = mysqli_query($dbc, "SELECT id,callsign,name,lat,lon,locator,known FROM users;") or die(mysqli_error($dbc));
while($user_row = mysqli_fetch_array($user_result))
{
	$user_id = $user_row['id'];
	$output[$i] = array();
	$output[$i]['id'] = $user_id;
	$output[$i]['callsign'] = $user_row['callsign'];
	$output[$i]['latitude'] = $user_row['lat'];
	$output[$i]['longitude'] = $user_row['lon'];
	$output[$i]['locator'] = $user_row['locator'];
	// Get User activity Data
	$session_result = mysqli_query($dbc, "SELECT activity FROM sessions WHERE user_id='{$user_id}';") or die(mysqli_error($dbc));
	$session_row = mysqli_fetch_array($session_result);
	$output[$i]['seconds_active'] = time() - date_format(date_create($session_row['activity']),'U'); // Used for icons
	$output[$i]['months_active'] = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%m');
	$output[$i]['hours_active'] = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%h');
	$output[$i]['days_active'] = date_interval_format(date_diff(date_create(), date_create($session_row['activity'])), '%d');
	if($user_row['known']=='1') {
		$output[$i]['known'] = '1';
		// TODO: Load Station Description Text
	} else {
		$output[$i]['known'] = '0';
	}
	$i++;
}

$json_output = json_encode($output);
echo $json_output;
mysql_end($dbc);
?>
