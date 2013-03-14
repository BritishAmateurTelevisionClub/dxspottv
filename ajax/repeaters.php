<?php
session_start();
include('../spot_login.php');

if(isset($_REQUEST['mode_id'])) { // Specific Mode

$mode_id = escape($dbc, $_REQUEST['mode_id']);

$output = array();
$i=1;
$repeater_result = mysqli_query($dbc, "SELECT * FROM repeaters WHERE mode='" . $mode_id . "';") or die(mysqli_error($dbc));
while($row = mysqli_fetch_array($repeater_result))
{
	$output[$i] = array();
	$output[$i]['callsign'] = $row['callsign'];
	$output[$i]['description'] = $row['Description'];
	$output[$i]['latitude'] = $row['lat'];
	$output[$i]['longitude'] = $row['lon'];
	$output[$i]['band'] = $row['band'];
}

$json_output = json_encode($output);
} else {
$repeater_result = mysqli_query($dbc, "SELECT * FROM repeaters;") or die(mysqli_error($dbc));
while($row = mysqli_fetch_array($repeater_result))
{
	$repeater['callsign'] = $row['callsign'];
	$repeater['description'] = $row['Description'];
	$repeater['latitude'] = $row['lat'];
	$repeater['longitude'] = $row['lon'];
	$repeater['band'] = $row['band'];
	$output[] = $repeater;
	unset($repeater);
}

$json_output = json_encode($output);
}

echo $json_output;
mysql_end($dbc);
?>
