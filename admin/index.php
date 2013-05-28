<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DXSpot.TV Admin</title>
<script src="/js/jquery-1.9.1.min.js"></script>
<script>
$(document).ready(function() {
	$.ajax({
		url: "/admin/ajax/currentActiveUsers.php",
		success: function( data ) {
			activeUserData = eval('(' + data + ')');
    		$('#lastMinCount').html('<b>'+activeUserData['lastMinute']+'</b>');
    		$('#lastHourCount').html('<b>'+activeUserData['lastHour']+'</b>');
    		$('#lastDayCount').html('<b>'+activeUserData['lastDay']+'</b>');
    		$('#lastWeekCount').html('<b>'+activeUserData['lastWeek']+'</b>');
    		$('#totalUsers').html('<b>'+activeUserData['totalUsers']+'</b>');
		}
	});
});
</script>
</head>
<body>
<h2>DXSpot.TV Admin Pages</h2>
<br>
<a href="repeaters.php"><h4>List of Repeaters</h4></a>

<a href="http://webirc.dxspot.tv/adminengine"><h4>qwebirc Admin Page</h4></a>

<h4>User Stats:</h4>
<ul>
<li>Active in Last Minute: <span id="lastMinCount">loading...</span></li>
<li>Active in Last Hour: <span id="lastHourCount">loading...</span></li>
<li>Active in Last Day: <span id="lastDayCount">loading...</span></li>
<li>Active in Last Week: <span id="lastWeekCount">loading...</span></li>
<li>Total Users: <span id="totalUsers">loading...</span></li>
</ul>
</body>
</html>
