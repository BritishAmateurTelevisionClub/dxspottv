<?php
include_once 'simple_html_dom.php';

$html = file_get_html("http://www.ukrepeater.net/repeaterlist5.htm");

$last_callsign = "NULL";
include('spot_login.php');
foreach($html->find('table.mix tr') as $row) {
	$callsign_array = explode("-",$row->find('td.minil', 0)->plaintext);
	$callsign = $callsign_array[0];
	if($callsign==$last_callsign) { // We have a duplicate!
		$band = $row->find('td.minil', 1)->plaintext;
		switch ($band) {
			case "70CM":
				$band_str = "70cm";
				break;
			case "23CM":
				$band_str = "23cm";
				break;
			case "13CM":
				$band_str = "13cm";
				break;
			case "9CM":
				$band_str = "9cm";
				break;
			case "6CM":
				$band_str = "6cm";
				break;
			case "3CM":
				$band_str = "3cm";
				break;
		}
		$tx_freq = $row->find('td.minil', 2)->plaintext;
		$rx_freq = $row->find('td.minil', 3)->plaintext;
		$rx_freq_2 = $row->find('td.minil', 4)->plaintext;
		if($rx_freq_2==" ") {
			$rx_freq_2=NULL;
		}
		$update_query = "UPDATE scraped_repeaters set is_{$band_str}='1', alt_tx_freq='{$tx_freq}', alt_rx_freq='{$rx_freq}', alt_rx_freq_2='{$rx_freq_2}' where callsign = '{$callsign}';";
		print $update_query . "<br>";
		mysqli_query($dbc, $update_query) or die(mysqli_error($dbc));
	} else { // New repeater, add to db..
		$band = $row->find('td.minil', 1)->plaintext;
		$is_70cm = 0;
		$is_23cm = 0;
		$is_13cm = 0;
		$is_9cm = 0;
		$is_6cm = 0;
		$is_3cm = 0;
		switch ($band) {
			case "70CM":
				$is_70cm = 1;
				break;
			case "23CM":
				$is_23cm = 1;
				break;
			case "13CM":
				$is_13cm = 1;
				break;
			case "9CM":
				$is_9cm = 1;
				break;
			case "6CM":
				$is_6cm = 1;
				break;
			case "3CM":
				$is_3cm = 1;
				break;
		}
		$tx_freq = $row->find('td.minil', 2)->plaintext;
		$rx_freq = $row->find('td.minil', 3)->plaintext;
		$rx_freq_2 = $row->find('td.minil', 4)->plaintext;
		if($rx_freq_2==" ") {
			$rx_freq_2=NULL;
		}
		$qth_r = $row->find('td.minil', 5)->plaintext;
		$qth = $row->find('td.minil', 6)->plaintext;
		$ngr = $row->find('td.minil', 7)->plaintext;
		$region = $row->find('td.minil', 8)->plaintext;
		$keeper_callsign = $row->find('td.minil', 9)->plaintext;
		$latlon = explode(",",$row->find('td.minil', 10)->plaintext);
		$lat = $latlon[0];
		$lon = $latlon[1];
		$status_text = $row->find('td.minil', 11)->plaintext;
		if($status_text=="OPERATIONAL") {
			$active = 1; // Online
		} else {
			$active = 0; // Offline
		}
		$insert_query="INSERT into scraped_repeaters (callsign, lat, lon, qth_r, qth, ngr, region, tx_freq, rx_freq, rx_freq_2, is_70cm, is_23cm, is_13cm, is_9cm, is_6cm, is_3cm, keeper_callsign, active) VALUES ('{$callsign}', '{$lat}', '{$lon}', '{$qth_r}', '{$qth}', '{$ngr}', '{$region}', '{$tx_freq}', '{$rx_freq}', '{$rx_freq_2}', '{$is_70cm}', '{$is_23cm}', '{$is_13cm}', '{$is_9cm}', '{$is_6cm}', '{$is_3cm}', '{$keeper_callsign}', '{$active}');";
		mysqli_query($dbc, $insert_query) or die(mysqli_error($dbc));
	}
	$last_callsign = $callsign;
}
mysql_end($dbc);
?>
