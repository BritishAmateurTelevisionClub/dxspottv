<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if(apc_exists('mapDataStatus')) {
	$final_output = apc_fetch('mapData');
} else {
	apc_store('mapDataStatus','Refreshing',1);
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
		if(mysqli_num_rows($session_result==1) {
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
	
	$repeater_result = mysqli_query($dbc, "SELECT * FROM repeaters;") or die(mysqli_error($dbc));
	while($row = mysqli_fetch_array($repeater_result))
	{
		$repeater['id'] = $row['id'];
		$repeater['callsign'] = $row['callsign'];
		$repeater['description'] = $row['description'];
		$repeater['latitude'] = $row['lat'];
		$repeater['longitude'] = $row['lon'];
		$repeater['qth_r'] = $row['qth_r'];
		$repeater['qth'] = $row['qth'];
		$repeater['tx_freq'] = $row['tx_freq'];
		$repeater['rx_freq'] = $row['rx_freq'];
		if($row['rx_freq_2']!=0) {
			$repeater['rx_freq_2'] = $row['rx_freq_2'];
		}
		if($row['alt_tx_freq']!=0) {
			$repeater['alt_tx_freq'] = $row['alt_tx_freq'];
			$repeater['alt_rx_freq'] = $row['alt_rx_freq'];
			if($row['alt_rx_freq_2']!=0) {
				$repeater['alt_rx_freq_2'] = $row['alt_rx_freq_2'];
			}
		}
		$repeater['is_70cm'] = $row['is_70cm'];
		$repeater['is_23cm'] = $row['is_23cm'];
		$repeater['is_13cm'] = $row['is_13cm'];
		$repeater['is_9cm'] = $row['is_9cm'];
		$repeater['is_6cm'] = $row['is_6cm'];
		$repeater['is_3cm'] = $row['is_3cm'];
		$repeater['description'] = $row['description'];
		$repeater['keeper'] = $row['keeper_callsign'];
		if($row['website']!='') {
			$repeater['website'] = $row['website'];
		}
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
		$output[$i]['seconds_ago'] = time() - date_format(date_create($spots_row['spot_time']),'U');
		$i++;
	}
	mysql_end($dbc);
	
	$full_output['spots'] = $output;
	$final_output = json_encode($full_output);
	apc_store('mapData', $final_output);
	apc_store('mapDataStatus','Valid',1);
}
print $final_output;
?>
