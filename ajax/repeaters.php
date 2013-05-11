<?php
session_start();
include('../spot_login.php');

$repeater_result = mysqli_query($dbc, "SELECT * FROM repeaters;") or die(mysqli_error($dbc));

while($row = mysqli_fetch_array($repeater_result))
{
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

print json_encode($output);

mysql_end($dbc);
?>
