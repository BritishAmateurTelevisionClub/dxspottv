<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Add Repeater</title>
<style>
#map-div {
    position: fixed;
    top: 1em;
    right: 1em;
}
#map_canvas {
	height: 300px;
	width: 400px;
}
</style>
<link href="/static/css/flick/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script src="/static/js/jquery-plus-ui.js"></script>
<?php
$logged_in = false;
$is_admin = false;
if(isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"])) {
	require_once('dxspottv_pdo.php');
	$sessions_stmt = $dbc->prepare("SELECT COUNT(1) FROM sessions WHERE user_id=? AND session_id=?;");
	$sessions_stmt->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
	$sessions_stmt->bindValue(2, $_COOKIE["session_key"]);
	$sessions_stmt->execute();
	if($sessions_stmt->rowCount()!=0) {
        $logged_in = true;
        $auth_stmt = $dbc->prepare("SELECT repeater_admin FROM users WHERE id=?;");
        $auth_stmt->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
        $auth_stmt->execute();
        $auth_stmt->bindColumn(1, $is_admin_n);
        $auth_stmt->fetch();
        if($is_admin_n==1) {
            $is_admin = true;
        }
    }
}
if($logged_in) {
if($is_admin) {
?>
<script src="/js/locator.js"></script>
<script>
var marker;
$(document).ready(function() {
    $('#edit_button').button().click( function() {
    	submitAdd();
	});
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=initialize'; // callback: initialize()
	document.body.appendChild(script);
});

function placeMarker(location) {
  if ( marker ) {
    marker.setPosition(location);
  } else {
    marker = new google.maps.Marker({
      position: location,
      map: map
    });
  }
}

function initialize() {
	google.maps.visualRefresh = true;
	var mapOptions = {
		zoom: 4,
		center: new google.maps.LatLng(50.5, 0),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		streetViewControl: false
	};

	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

	google.maps.event.addListener(map, 'click', function(event) {
		$('#lat').val(event.latLng.lat());
		$('#lon').val(event.latLng.lng());
		placeMarker(event.latLng);
	});
}

function submitAdd() {
	$.ajax({
		url: "/repeater-admin/ajax/addRepeater.php",
		type: "GET",
		data: {
			callsign: $('#input_callsign').val(),
			locator: $('#input_locator').val(),
			location: $('#input_location').val(),
			lat: $('#lat').val(),
			lon: $('#lon').val(),
			height: $('#input_height').val(),
			is_2m: $('#input_is_2m').val(),
			is_70cm: $('#input_is_70cm').val(),
			is_23cm: $('#input_is_23cm').val(),
			is_13cm: $('#input_is_13cm').val(),
			is_9cm: $('#input_is_9cm').val(),
			is_6cm: $('#input_is_6cm').val(),
			is_3cm: $('#input_is_3cm').val(),
			tx1: $('#input_tx1').val(),
			tx2: $('#input_tx2').val(),
			tx3: $('#input_tx3').val(),
			tx4: $('#input_tx4').val(),
			tx5: $('#input_tx5').val(),
			tx6: $('#input_tx6').val(),
			tx7: $('#input_tx7').val(),
			tx8: $('#input_tx8').val(),
			tx9: $('#input_tx9').val(),
			rx1: $('#input_rx1').val(),
			rx2: $('#input_rx2').val(),
			rx3: $('#input_rx3').val(),
			rx4: $('#input_rx4').val(),
			rx5: $('#input_rx5').val(),
			rx6: $('#input_rx6').val(),
			rx7: $('#input_rx7').val(),
			rx8: $('#input_rx8').val(),
			rx9: $('#input_rx9').val(),
			description: $('#input_description').val(),
			website: $('#input_website').val(),
			keeper: $('#input_keeper').val(),
			active: $('#input_active').val()
		},
		success: function( data ) {
			retData = eval('(' + data + ')');
			if (typeof retData['success'] != 'undefined') {
				$('#editStatus').html("<font color=green>Added Successfully.</font>");
				$('#editStatus').show();
				$('#editStatus').fadeOut(1500);
			} else {
				switch(retData['error'])
				{
				case "1":
					$('#editStatus').html("<font color=red>Form Error.</font>");
					break;
				case "2":
					$('#editStatus').html("<font color=red>MySQL Error.</font>");
					break;
				}
				$('#editStatus').show();
				$('#editStatus').fadeOut(8000);
			}
		}
	});
}
</script>
</head>
<body>
<h1>Admin</h1>
<a href="/repeater-admin/"><h3>Back to List of Repeaters</h2></a>
<h2>Add Repeater</h2>
<b>Callsign:</b>&nbsp;<input type=text id="input_callsign"></input><br>
<b>Locator:</b>&nbsp;<input type=text id="input_locator"></input><br>
<b>Antenna Height:</b>&nbsp;<input type=text id="input_height" value="0"></input>m<br>
<b>Location:</b>&nbsp;<input type=text id="input_location"></input><br>
<h3>Output Bands: (0 or 1)</h3>
<b>2m:</b>&nbsp;<input type=text id="input_is_2m"></input><br>
<b>70cm:</b>&nbsp;<input type=text id="input_is_70cm"></input><br>
<b>23cm:</b>&nbsp;<input type=text id="input_is_23cm"></input><br>
<b>13cm:</b>&nbsp;<input type=text id="input_is_13cm"></input><br>
<b>9cm:</b>&nbsp;<input type=text id="input_is_9cm"></input><br>
<b>6cm:</b>&nbsp;<input type=text id="input_is_6cm"></input><br>
<b>3cm:</b>&nbsp;<input type=text id="input_is_3cm"></input><br>
<h3>TX</h3>
<b>1:</b>&nbsp;<input type=text id="input_tx1"></input>&nbsp;MHz<br>
<b>2:</b>&nbsp;<input type=text id="input_tx2"></input>&nbsp;MHz<br>
<b>3:</b>&nbsp;<input type=text id="input_tx3"></input>&nbsp;MHz<br>
<b>4:</b>&nbsp;<input type=text id="input_tx4"></input>&nbsp;MHz<br>
<b>5:</b>&nbsp;<input type=text id="input_tx5"></input>&nbsp;MHz<br>
<b>6:</b>&nbsp;<input type=text id="input_tx6"></input>&nbsp;MHz<br>
<b>7:</b>&nbsp;<input type=text id="input_tx7"></input>&nbsp;MHz<br>
<b>8:</b>&nbsp;<input type=text id="input_tx8"></input>&nbsp;MHz<br>
<b>9:</b>&nbsp;<input type=text id="input_tx9"></input>&nbsp;MHz<br>
<h3>RX</h3>
<b>1:</b>&nbsp;<input type=text id="input_rx1"></input>&nbsp;MHz<br>
<b>2:</b>&nbsp;<input type=text id="input_rx2"></input>&nbsp;MHz<br>
<b>3:</b>&nbsp;<input type=text id="input_rx3"></input>&nbsp;MHz<br>
<b>4:</b>&nbsp;<input type=text id="input_rx4"></input>&nbsp;MHz<br>
<b>5:</b>&nbsp;<input type=text id="input_rx5"></input>&nbsp;MHz<br>
<b>6:</b>&nbsp;<input type=text id="input_rx6"></input>&nbsp;MHz<br>
<b>7:</b>&nbsp;<input type=text id="input_rx7"></input>&nbsp;MHz<br>
<b>8:</b>&nbsp;<input type=text id="input_rx8"></input>&nbsp;MHz<br>
<b>9:</b>&nbsp;<input type=text id="input_rx9"></input>&nbsp;MHz<br>
<b>Description:</b>&nbsp;<textarea rows="4" cols="50" id="input_description"></textarea><br>
<b>Website:</b>&nbsp;<input type=text id="input_website"></input><br>
<b>Keeper:</b>&nbsp;<input type=text id="input_keeper"></input><br>
<b>Active:</b>&nbsp;<input type=text id="input_active" value="1"></input> (1 or 0)<br>
<button class="edit-button reduce-font-size" id="edit_button">Submit</button>&nbsp;<span id="editStatus"></span>
<div id="map-div">
<center>
<h3>Set Station Location</h3>
<div id="map_canvas"></div>
</center>
<br>
Simply zoom in and click on the map to set your location.<br><br>
<i>Your Latitude and Longtitude will be filled in automatically.<i>
<br>
<br>
<label class="register_labels"><b>Latitude:</b>&nbsp;</label><input type=text name='lat' id='lat' class="required number" minlength="4" />
<br>
<label class="register_labels"><b>Longitude:</b>&nbsp;</label><input type=text name='lon' id='lon' class="required number" minlength="4" />
</div>
<?php
} else { // Logged In, not allowed
?>
</head>
<body>
<h2 style="color: red;">You are not a Repeater Admin. Please contact <a href="mailto:dxspottv.feedback@gmail.com">dxspottv.feedback@gmail.com</a> for access</h2>
<?php
}
} else { // Not Logged In
?>
</head>
<body>
<h2 style="color: red;">You are not logged in, please <a href="/">Log In</a> and try again.</h2>
<?php
}
?>
</body>
</html>
