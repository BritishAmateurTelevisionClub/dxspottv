<?php
session_start();

if(!(isset($_REQUEST['timespan']) && isset($_REQUEST['bands']))) {
	die("invalid request");
} else {

include('../spot_login.php');

$bands = escape($dbc, $_REQUEST['bands']);
switch ($bands) {
	case "all":
		$min_freq = 1; // 1MHz
		$max_freq = 1e6; // 1THz
		break;
	case "70cm":
		$min_freq = 430;
		$max_freq = 440;
		break;
	case "23cm":
		$min_freq = 1240;
		$max_freq = 1325;
		break;
	case "13cm": // 13cm & above
		$min_freq = 2310;
		$max_freq = 1e6;
		break;
}

$timespan = escape($dbc, $_REQUEST['timespan']);
switch ($timespan) {
	case "all":
		$spots_result = mysqli_query($dbc, "SELECT * FROM spots;") or die(mysqli_error($dbc));
		break;
	case "year":
		$spots_result = mysqli_query($dbc, "SELECT * FROM spots WHERE time > (NOW() - INTERVAL 1 YEAR) AND frequency BETWEEN '" . $min_freq . "' AND '" . $max_freq . "';") or die(mysqli_error($dbc));
		break;
	case "6months":
		$spots_result = mysqli_query($dbc, "SELECT * FROM spots WHERE time > (NOW() - INTERVAL 6 MONTH) AND frequency BETWEEN '" . $min_freq . "' AND '" . $max_freq . "';") or die(mysqli_error($dbc));
		break;
	case "1month":
		$spots_result = mysqli_query($dbc, "SELECT * FROM spots WHERE time > (NOW() - INTERVAL 1 MONTH) AND frequency BETWEEN '" . $min_freq . "' AND '" . $max_freq . "';") or die(mysqli_error($dbc));
		break;
	case "1week":
		$spots_result = mysqli_query($dbc, "SELECT * FROM spots WHERE time > (NOW() - INTERVAL 1 WEEK) AND frequency BETWEEN '" . $min_freq . "' AND '" . $max_freq . "';") or die(mysqli_error($dbc));
		break;
	case "24hours":
		$spots_result = mysqli_query($dbc, "SELECT * FROM spots WHERE time > (NOW() - INTERVAL 24 HOUR) AND frequency BETWEEN '" . $min_freq . "' AND '" . $max_freq . "';") or die(mysqli_error($dbc));
		break;
	case "12hours":
		$spots_result = mysqli_query($dbc, "SELECT * FROM spots WHERE time > (NOW() - INTERVAL 12 HOUR) AND frequency BETWEEN '" . $min_freq . "' AND '" . $max_freq . "';") or die(mysqli_error($dbc));
		break;
}

$output = array();
while($row = mysqli_fetch_array($spots_result))
{
	$spot['mode_id'] = $row['mode_id'];
	$spot['frequency'] = $row['frequency'];
	$spot['primary_id'] = $row['primary_id'];
	$spot['secondary_id'] = $row['secondary_id'];
	$spot['time'] = $row['time'];
	$spot['comments'] = $row['comments'];
	$output[] = $spot;
	unset($spot);
}

$json_output = json_encode($output);
}

echo $json_output;
mysql_end($dbc);
?>
