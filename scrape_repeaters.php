<?php
include_once 'simple_html_dom.php';

$html = file_get_html("http://www.ukrepeater.net/repeaterlist5.htm");
$output = array();
$i=1;
foreach($html->find('table.mix tr') as $row) {
	$output[$i] = array();
	$output[$i]['callsign'] = $row->find('td.minil', 0)->plaintext;
	$output[$i]['band'] = $row->find('td.minil', 1)->plaintext;
	$output[$i]['tx_freq'] = $row->find('td.minil', 2)->plaintext;
	$output[$i]['rx_freq1'] = $row->find('td.minil', 3)->plaintext;
	$secondary_rx_freq = $row->find('td.minil', 4)->plaintext;
	if($secondary_rx_freq!=" ") {
		$output[$i]['rx_freq2'] = $secondary_rx_freq;
	}
	$output[$i]['qthr'] = $row->find('td.minil', 5)->plaintext;
	$output[$i]['qth'] = $row->find('td.minil', 6)->plaintext;
	$output[$i]['ngr'] = $row->find('td.minil', 7)->plaintext;
	$output[$i]['region'] = $row->find('td.minil', 8)->plaintext;
	$output[$i]['keeper'] = $row->find('td.minil', 9)->plaintext;
	$latlon = explode(",",$row->find('td.minil', 10)->plaintext);
	$output[$i]['lat'] = $latlon[0];
	$output[$i]['lon'] = $latlon[1];
	$status_text = $row->find('td.minil', 11)->plaintext;
	if($status_text=="OPERATIONAL") {
		$output[$i]['status'] = 1; // Online
	} else {
		$output[$i]['status'] = 0; // Offline
	}
	$i++;
}
print json_encode($output);
?>
