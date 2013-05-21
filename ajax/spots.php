<?php
session_start();
include('../spot_login.php');

$output = array();
$i=1;
$spots_result = mysqli_query($dbc, "SELECT * FROM spots ORDER BY id DESC;") or die(mysqli_error($dbc));
while($spots_row = mysqli_fetch_array($spots_result))
{
	$output[$i] = array();
	$output[$i]['id'] = $spots_row['id'];
	$output[$i]['mode_id'] = $spots_row['mode_id'];
	$output[$i]['frequency'] = $spots_row['frequency'];
	$output[$i]['primary_id'] = $spots_row['primary_id'];
	$output[$i]['secondary_id'] = $spots_row['secondary_id'];
	$output[$i]['secondary_isrepeater'] = $spots_row['secondary_isrepeater'];
	$output[$i]['time'] = $spots_row['spot_time'];
	$output[$i]['comments'] = $spots_row['comments'];
	$output[$i]['seconds_ago'] = time() - date_format(date_create($spots_row['time']),'U');
	$output[$i]['minutes_ago'] = date_interval_format(date_diff(date_create(), date_create($spots_row['time'])), '%i');
	$output[$i]['hours_ago'] = date_interval_format(date_diff(date_create(), date_create($spots_row['time'])), '%H');
	$output[$i]['days_ago'] = date_interval_format(date_diff(date_create(), date_create($spots_row['time'])), '%d');
	$output[$i]['months_ago'] = date_interval_format(date_diff(date_create(), date_create($spots_row['time'])), '%m');
	$i++;
}

$json_output = json_encode($output);
echo $json_output;
mysql_end($dbc);
?>
