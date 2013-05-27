<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DXSpot.TV Admin</title>
<script src="/js/jquery-1.9.1.min.js"></script>
<script>
$(document).ready(function() {
	$.ajax({
		url: "/ajax/currentActiveUsers.php",
		success: function( data ) {
			myJSONObject = eval('(' + data + ')');
    		$('#lastMinCount').html('<b>'+myJSONObject['lastMinute']+'</b>');
    		$('#lastHourCount').html('<b>'+myJSONObject['lastHour']+'</b>');
    		$('#lastDayCount').html('<b>'+myJSONObject['lastDay']+'</b>');
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

<h4>Current Stats:</h4>
<ul>
<li>Last Minute: <span id="lastMinCount">loading...</span></li>
<li>Last Hour: <span id="lastHourCount">loading...</span></li>
<li>Last Day: <span id="lastDayCount">loading...</span></li>
</ul>
</body>
</html>
