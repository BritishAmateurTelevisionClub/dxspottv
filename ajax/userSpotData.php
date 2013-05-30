<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if(apc_exists('userSpotDataStatus')) {
	$final_output = apc_fetch('userSpotData');
} else {
	apc_store('userSpotStatus','Refreshing',1);
	require_once('spot_login.php');
	$full_output = array();
	$output = array();
	$i=1;
	$user_result = mysqli_query($dbc, "SELECT id,callsign,name,lat,lon,locator,known,station_desc FROM users;") or die(mysqli_error($dbc));
	while($user_row = mysqli_fetch_array($user_result))
	{
		$user_id = $user_row['id'];
		$output[$i] = array();
		$output[$i]['id'] = $user_id;
		$output[$i]['callsign'] = $user_row['callsign'];
		$output[$i]['latitude'] = $user_row['lat'];
		$output[$i]['longitude'] = $user_row['lon'];
		$output[$i]['locator'] = $user_row['locator'];
		$output[$i]['desc'] = $user_row['station_desc'];
		// Get User activity Data
		$session_result = mysqli_query($dbc, "SELECT activity FROM sessions WHERE user_id='{$user_id}';") or die(mysqli_error($dbc));
		if(mysqli_num_rows($session_result)==1) {
			$session_row = mysqli_fetch_array($session_result);
			$output[$i]['seconds_active'] = time() - date_format(date_create($session_row['activity']),'U'); // Used for icons
		} else { // No session exists
			$output[$i]['seconds_active'] = 1000; // Large, won't be shown on map.
		}
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
		$output[$i]['seconds_ago'] = time() - date_format(date_create($spots_row['spot_time']),'U');
		$i++;
	}
	mysql_end($dbc);
	
	$full_output['spots'] = $output;
	$final_output = json_encode($full_output);
	apc_store('userSpotData', $final_output);
	apc_store('userSpotDataStatus','Valid',1);
}
print $final_output;
?>
