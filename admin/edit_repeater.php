<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Repeater</title>
<link href="/css/flick/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script src="/js/jquery-plus-ui.js"></script>
<script>
var repeater_id;
$(document).ready(function() {
	var parts = window.location.search.substr(1).split("&");
	var $_GET = {};
	for (var i = 0; i < parts.length; i++) {
		var temp = parts[i].split("=");
		$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
	}
	repeater_id = $_GET.id;
	
	$('#edit_button').button().click( function() {
    	submitEdit();
	});
	
	$.ajax({
		url: "/admin/ajax/repeaterInfo.php",
		type: "POST",
		data: {
			repeater_id: repeater_id
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
    		$('#input_active').val(repeaterData['active']);
		}
	});
});
function submitEdit() {
	$.ajax({
		url: "/admin/ajax/editRepeater.php",
		type: "POST",
		data: {
			repeater: repeater_id,
			callsign: $('#input_callsign').val(),
			locator: $('#input_location').val(),
			location: $('#input_location').val(),
			description: $('#input_description').val(),
			website: $('#input_website').val(),
			keeper: $('#input_keeper').val(),
			active: $('#input_active').val()
		},
		success: function( data ) {
			$('#editStatus').html("<font color=green>Changed.</font>"); // Clear status
			$('#editStatus').fadeOut(1000);
		}
	});
}
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
<b>Active:</b>&nbsp<input type=text id="input_active"></input> (1 or 0)<br>
<button class="edit-button reduce-font-size" id="edit_button">Submit</button>&nbsp;<span id="editStatus"></span>
</body>
</html>
