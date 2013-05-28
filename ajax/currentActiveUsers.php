<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if(apc_exists('currentActiveUsersStatus')) {
	$output = apc_fetch('currentActiveUsers');
} else {
	apc_store('currentActiveUsersStatus','Refreshing',1);
	require_once('spot_login.php');

	$count = 0;
	$times = array();
	$session_result = mysqli_query($dbc, "SELECT activity FROM sessions;") or die(mysqli_error($dbc));
	while($row = mysqli_fetch_array($session_result))
	{
		$times[] = time() - date_format(date_create($row['activity']),'U');
		$count++;
	}
	mysql_end($dbc);

	$lessThanMinute = 0;
	$lessThanHour = 0;
	$lessThanDay = 0;
	$lessThanWeek = 0;

	foreach($times as $seconds) {
		if($seconds<60) {
			$lessThanMinute++;
		} 
		if($seconds<3600) {
			$lessThanHour++;
		} 
		if($seconds<86400) {
			$lessThanDay++;
		}
		if($seconds<604800) {
			$lessThanWeek++;
		}
	}

	$output = array();
	$output['lastMinute'] = $lessThanMinute;
	$output['lastHour'] = $lessThanHour;
	$output['lastDay'] = $lessThanDay;
	$output['lastWeek'] = $lessThanWeek;
	$output['totalUsers'] = $count;
	
	apc_store('currentActiveUsers', $output);
	apc_store('currentActiveUsersStatus','Valid',3);
}
print json_encode($output);
?>
