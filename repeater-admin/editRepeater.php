<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Repeater</title>
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
$(document).ready(function() {
    $('#edit_button').button().click( function() {
    	submitEdit();
	});
	
	var parts = window.location.search.substr(1).split("&");
	var $_GET = {};
	for (var i = 0; i < parts.length; i++) {
		var temp = parts[i].split("=");
		$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
	}
	repeater_id = $_GET.id;
	
	$.ajax({
		url: "/repeater-admin/ajax/repeaterInfo.php",
		type: "GET",
		data: {
			repeater_id: repeater_id
		},
		dataType: 'json',
		success: function( data ) {
    		$('#input_callsign').val(data.callsign);
    		$('#input_locator').val(data.qth_r);
    		$('#input_location').val(data.qth);
    		$('#input_description').val(data.description);
    		$('#input_website').val(data.website);
    		$('#input_keeper').val(data.keeper);
    		$('#input_active').val(data.active);
    		
    		$('#input_is_2m').val(data.is_2m),
			$('#input_is_70cm').val(data.is_70cm),
			$('#input_is_23cm').val(data.is_23cm),
			$('#input_is_13cm').val(data.is_13cm),
			$('#input_is_9cm').val(data.is_9cm),
			$('#input_is_6cm').val(data.is_6cm),
			$('#input_is_3cm').val(data.is_3cm),
    		
    		$('#input_tx1').val(data.tx1);
    		$('#input_tx2').val(data.tx2);
    		$('#input_tx3').val(data.tx3);
    		$('#input_tx4').val(data.tx4);
    		$('#input_tx5').val(data.tx5);
    		$('#input_tx6').val(data.tx6);
    		$('#input_tx7').val(data.tx7);
    		$('#input_tx8').val(data.tx8);
    		$('#input_tx9').val(data.tx9);
    		
    		$('#input_rx1').val(data.rx1);
    		$('#input_rx2').val(data.rx2);
    		$('#input_rx3').val(data.rx3);
    		$('#input_rx4').val(data.rx4);
    		$('#input_rx5').val(data.rx5);
    		$('#input_rx6').val(data.rx6);
    		$('#input_rx7').val(data.rx7);
    		$('#input_rx8').val(data.rx8);
    		$('#input_rx9').val(data.rx9);
		}
	});
});

function submitEdit() {
    var add_latlon = LoctoLatLon($('#input_locator').val());
	$.ajax({
		url: "/repeater-admin/ajax/editRepeater.php",
		type: "GET",
		data: {
		    id: repeater_id,
			callsign: $('#input_callsign').val(),
			locator: $('#input_locator').val(),
			location: $('#input_location').val(),
			lat: add_latlon[0],
			lon: add_latlon[1],
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
				$('#editStatus').html("<font color=green>Added.</font>");
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
				$('#editStatus').fadeOut(3000);
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
