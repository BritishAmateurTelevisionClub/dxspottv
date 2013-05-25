<?php
include_once 'simple_html_dom.php';

$html = file_get_html("http://www.ukrepeater.net/repeaterlist5.htm");
$duplicate = 0;
include('spot_login.php');
foreach($html->find('table.mix tr') as $row) {
	switch ($duplicate) {
		case 1:
			$duplicate = 0;
			$callsign_array = explode("-",$row->find('td.minil', 0)->plaintext);
			$callsign = $callsign_array[0];
			switch ($band) {
				case "70CM":
					$update_query = "UPDATE scraped_repeaters set is_70cm='1' where callsign = '{$callsign}';";
					break;
				case "23CM":
					$update_query = "UPDATE scraped_repeaters set is_23cm='1' where callsign = '{$callsign}';";
					break;
				case "13CM":
					$update_query = "UPDATE scraped_repeaters set is_13cm='1' where callsign = '{$callsign}';";
					break;
				case "9CM":
					$update_query = "UPDATE scraped_repeaters set is_9cm='1' where callsign = '{$callsign}';";
					break;
				case "6CM":
					$update_query = "UPDATE scraped_repeaters set is_6cm='1' where callsign = '{$callsign}';";
					break;
				case "3CM":
					$update_query = "UPDATE scraped_repeaters set is_3cm='1' where callsign = '{$callsign}';";
					break;
			}
			mysqli_query($dbc, $update_query) or die(mysqli_error($dbc));
			break;
		case 0:
			$callsign_array = explode("-",$row->find('td.minil', 0)->plaintext);
			$callsign = $callsign_array[0];
			if($callsign_array[0]=="1") {
				$duplicate = 1;
			}
			$band = $row->find('td.minil', 1)->plaintext;
			switch ($band) {
				case "70CM":
					$is_70cm = 1;
					$is_23cm = 0;
					$is_13cm = 0;
					$is_9cm = 0;
					$is_6cm = 0;
					$is_3cm = 0;
					break;
				case "23CM":
					$is_70cm = 0;
					$is_23cm = 1;
					$is_13cm = 0;
					$is_9cm = 0;
					$is_6cm = 0;
					$is_3cm = 0;
					break;
				case "13CM":
					$is_70cm = 0;
					$is_23cm = 0;
					$is_13cm = 1;
					$is_9cm = 0;
					$is_6cm = 0;
					$is_3cm = 0;
					break;
				case "9CM":
					$is_70cm = 0;
					$is_23cm = 0;
					$is_13cm = 0;
					$is_9cm = 1;
					$is_6cm = 0;
					$is_3cm = 0;
					break;
				case "6CM":
					$is_70cm = 0;
					$is_23cm = 0;
					$is_13cm = 0;
					$is_9cm = 0;
					$is_6cm = 1;
					$is_3cm = 0;
					break;
				case "3CM":
					$is_70cm = 0;
					$is_23cm = 0;
					$is_13cm = 0;
					$is_9cm = 0;
					$is_6cm = 0;
					$is_3cm = 1;
					break;
			}
			$tx_freq = $row->find('td.minil', 2)->plaintext;
			$rx_freq = $row->find('td.minil', 3)->plaintext;
			$secondary_rx_freq = $row->find('td.minil', 4)->plaintext;
			if($secondary_rx_freq!=" ") {
				$output[$i]['rx_freq2'] = $secondary_rx_freq;
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
			if($secondary_rx_freq==" ") {
				$insert_query="INSERT into scraped_repeaters (callsign, lat, lon, qth_r, qth, ngr, region, tx_freq, rx_freq, is_70cm, is_23cm, is_13cm, is_9cm, is_6cm, is_3cm, keeper_callsign, active) VALUES ('{$callsign}', '{$lat}', '{$lon}', '{$qth_r}', '{$qth}', '{$ngr}', '{$region}', '{$tx_freq}', '{$rx_freq}', '{$is_70cm}', '{$is_23cm}', '{$is_13cm}', '{$is_9cm}', '{$is_6cm}', '{$is_3cm}', '{$keeper_callsign}', '{$active}');";
			} else {
				$insert_query="INSERT into scraped_repeaters (callsign, lat, lon, qth_r, qth, ngr, region, tx_freq, rx_freq, has_secondary_rx, secondary_rx_freq, is_70cm, is_23cm, is_13cm, is_9cm, is_6cm, is_3cm, keeper_callsign, active) VALUES ('{$callsign}', '{$lat}', '{$lon}', '{$qth_r}', '{$qth}', '{$ngr}', '{$region}', '{$tx_freq}', '{$rx_freq}', '1', '{$secondary_rx_freq}', '{$is_70cm}', '{$is_23cm}', '{$is_13cm}', '{$is_9cm}', '{$is_6cm}', '{$is_3cm}', '{$keeper_callsign}', '{$active}');";
			}
			mysqli_query($dbc, $insert_query) or die(mysqli_error($dbc));
			break;
	}
}
mysql_end($dbc);
?>
