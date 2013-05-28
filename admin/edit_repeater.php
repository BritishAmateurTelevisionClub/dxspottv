<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Repeater</title>
<script src="/js/jquery-1.9.1.min.js"></script>
<script>
$(document).ready(function() {
	var parts = window.location.search.substr(1).split("&");
	var $_GET = {};
	for (var i = 0; i < parts.length; i++) {
		var temp = parts[i].split("=");
		$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
	}
	$.ajax({
		url: "/admin/ajax/repeaterInfo.php",
		type: "POST",
		data: {
			repeater_id: $_GET.id
		},
		success: function( data ) {
			repeaterData = eval('(' + data + ')');
    		$('#input_callsign').val(repeaterData['callsign']);
    		$('#input_locator').val(repeaterData['qth_r']);
    		$('#input_location').val(repeaterData['qth']);
    		$('#input_description').val(repeaterData['description']);
    		if (typeof repeaterData['website'] != 'undefined') {
    			$('#input_website').val(repeaterData['website']);
    		}
    		$('#input_keeper').val(repeaterData['keeper']);
		}
	});
});
</script>
</head>
<body>
<h1>Admin</h1>
<h2>Edit Repeater</h2>
<b>Callsign:</b>&nbsp<input type=text id="input_callsign"></input><br>
<b>Locator:</b>&nbsp<input type=text id="input_locator"></input><br>
<b>Location:</b>&nbsp<input type=text id="input_location"></input><br>
<b>Description:</b>&nbsp<input type=text id="input_description"></input><br>
<b>Website:</b>&nbsp<input type=text id="input_website"></input><br>
<b>Keeper:</b>&nbsp<input type=text id="input_keeper"></input><br>
</body>
</html>
