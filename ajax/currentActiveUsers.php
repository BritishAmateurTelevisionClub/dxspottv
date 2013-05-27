<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if(apc_exists('currentActiveUsersStatus')) {
	$output = apc_fetch('currentActiveUsers');
} else {
	apc_store('currentActiveUsers','Refreshing',1);
	include('spot_login.php');

	$times = array();
	$session_result = mysqli_query($dbc, "SELECT activity FROM sessions;") or die(mysqli_error($dbc));
	while($row = mysqli_fetch_array($session_result))
	{
	
		$activity = time() - date_format(date_create($row['activity']),'U');
		$times[] = $activity;
		unset($activity);
	}
	mysql_end($dbc);

	$lessThanMinute = 0;
	$lessThanHour = 0;
	$lessThanDay = 0;

	foreach($times as $seconds) {
		if($seconds<60) {
			$lessThanMinute++;
		} else if($seconds<3600) {
			$lessThanHour++;
		} else if($seconds<86400) {
			$lessThanDay++;
		}
	}

	$output = array();
	$output['lastMinute'] = $lessThanMinute;
	$output['lastHour'] = $lessThanHour;
	$output['lastDay'] = $lessThanDay;
	
	apc_store('currentActiveUsers', $output);
	apc_store('currentActiveUsersStatus','Valid',3);
}
print json_encode($output);
?>
