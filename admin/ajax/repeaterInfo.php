<?php
session_start();
if(!(isset($_REQUEST["repeater_id"]))) {
	print "Error: No ID requested.";
} else {

include('../../spot_login.php');

$request_id = mysqli_real_escape_string($dbc, $_REQUEST["repeater_id"]);

$output = array();
$i=1;
$repeater_result = mysqli_query($dbc, "SELECT * FROM repeaters WHERE id ={$request_id};") or die(mysqli_error($dbc));
while($repeater_row = mysqli_fetch_array($repeater_result))
{
	$output[$i] = array();
	$output[$i]['id'] = $request_id;
	$output[$i]['callsign'] = $repeater_row['callsign'];
	$output[$i]['latitude'] = $repeater_row['lat'];
	$output[$i]['longitude'] = $repeater_row['lon'];
	$output[$i]['is_70cm'] = $repeater_row['is_70cm'];
	$output[$i]['is_23cm'] = $repeater_row['is_23cm'];
	$output[$i]['is_13cm'] = $repeater_row['is_13cm'];
	$output[$i]['is_13cm'] = $repeater_row['is_13cm'];
	$output[$i]['active'] = $repeater_row['active'];
	$output[$i]['description'] = $repeater_row['Description'];
	$i++;
}

$json_output = json_encode($output);
echo $json_output;
mysql_end($dbc);
}
?>
