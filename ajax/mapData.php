<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if(apc_exists('mapData')) {
	$final_output = apc_fetch('mapData');
} else {
	include('../spot_login.php');
	$full_output = array();
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
	$full_output['users'] = $output;
	unset($output);
	
	$repeater_result = mysqli_query($dbc, "SELECT * FROM repeaters;") or die(mysqli_error($dbc));
	while($row = mysqli_fetch_array($repeater_result))
	{
		$repeater['id'] = $row['id'];
		$repeater['callsign'] = $row['callsign'];
		$repeater['description'] = $row['Description'];
		$repeater['latitude'] = $row['lat'];
		$repeater['longitude'] = $row['lon'];
		$repeater['is_70cm'] = $row['is_70cm'];
		$repeater['is_23cm'] = $row['is_23cm'];
		$repeater['is_13cm'] = $row['is_13cm'];
		$repeater['is_3cm'] = $row['is_3cm'];
		$repeater['active'] = $row['active'];
		$output[] = $repeater;
		unset($repeater);
	}
	$full_output['repeaters'] = $output;
	unset($output);
	
	$output = array();
	$i=1;
	$spots_result = mysqli_query($dbc, "SELECT * FROM spots ORDER BY id DESC;") or die(mysqli_error($dbc));
	while($spots_row = mysqli_fetch_array($spots_result))
	{
		$output[$i] = array();
		$output[$i]['id'] = $spots_row['id'];
		$output[$i]['mode_id'] = $spots_row['mode_id'];
		$output[$i]['band_id'] = $spots_row['band_id'];
		$output[$i]['primary_id'] = $spots_row['primary_id'];
		$output[$i]['secondary_id'] = $spots_row['secondary_id'];
		$output[$i]['secondary_isrepeater'] = $spots_row['secondary_isrepeater'];
		$output[$i]['time'] = $spots_row['spot_time'];
		$output[$i]['comments'] = $spots_row['comments'];
		$output[$i]['seconds_ago'] = time() - date_format(date_create($spots_row['time']),'U');
		$output[$i]['minutes_ago'] = date_interval_format(date_diff(date_create(), date_create($spots_row['time'])), '%i');
		$output[$i]['hours_ago'] = date_interval_format(date_diff(date_create(), date_create($spots_row['time'])), '%H');
		$output[$i]['days_ago'] = date_interval_format(date_diff(date_create(), date_create($spots_row['time'])), '%d');
		$output[$i]['months_ago'] = date_interval_format(date_diff(date_create(), date_create($spots_row['time'])), '%m');
		$i++;
	}
	mysql_end($dbc);
	
	$full_output['spots'] = $output;
	$final_output = json_encode($full_output);
	apc_add('mapData', $final_output, 1);
}
print $final_output;
?>