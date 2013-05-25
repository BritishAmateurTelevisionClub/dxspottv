<?php
include_once 'simple_html_dom.php';

$html = file_get_html("http://www.ukrepeater.net/repeaterlist5.htm");
$output = array();
$i=1;
foreach($html->find('table.mix tr') as $row) {
	$output[$i] = array();
	$output[$i]['callsign'] = $row->find('td.minil', 0)->plaintext;
	$output[$i]['band'] = $row->find('td.minil', 1)->plaintext;
	$output[$i]['output_freq'] = $row->find('td.minil', 2)->plaintext;
	$output[$i]['input_freq1'] = $row->find('td.minil', 3)->plaintext;
	$i++;
}
print json_encode($output);
?>
