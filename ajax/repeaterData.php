<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if(apc_exists('repeaterDataStatus')) {
	$final_output = apc_fetch('repeaterData');
} else {
	apc_store('repeaterDataStatus','Refreshing',1);
	require_once('spot_login.php');
	
	$output = array();
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
	mysql_end($dbc);
	
	$final_output = json_encode($output);
	apc_store('repeaterData', $final_output);
	apc_store('repeaterDataStatus','Valid',5);
}
print $final_output;
?>
