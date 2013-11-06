<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Add Repeater</title>
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
    	submitAdd();
	});
});

function submitAdd() {
    var add_latlon = LoctoLatLon($('#input_locator').val());
    var is_2m=0;
    var is_70cm=0;
    var is_23cm=0;
    var is_13cm=0;
    var is_9cm=0;
    var is_6cm=0;
    var is_3cm=0;
    switch ($('#input_band').val()) {
        case "2m":
          is_2m=1;
          break;
        case "70cm":
          is_70cm=1;
          break;
        case "23cm":
          is_23cm=1;
          break;
        case "13cm":
          is_13cm=1;
          break;
        case "9cm":
          is_9cm=1;
          break;
        case "6cm":
          is_6cm=1;
          break;
        case "3cm":
          is_3cm=1;
          break;
    }
	$.ajax({
		url: "/repeater-admin/ajax/addRepeater.php",
		type: "GET",
		data: {
			callsign: $('#input_callsign').val(),
			locator: $('#input_locator').val(),
			location: $('#input_location').val(),
			lat: add_latlon[0],
			lon: add_latlon[1],
			height: $('#input_height').val(),
			is_2m: is_2m,
			is_70cm: is_70cm,
			is_23cm: is_23cm,
			is_13cm: is_13cm,
			is_9cm: is_9cm,
			is_6cm: is_6cm,
			is_3cm: is_3cm,
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
<a href="/repeater-admin/admin/index.php"><h3>Back to List of Repeaters</h2></a>
<h2>Add Repeater</h2>
<b>Callsign:</b>&nbsp;<input type=text id="input_callsign"></input><br>
<b>Locator:</b>&nbsp;<input type=text id="input_locator"></input><br>
<b>Antenna Height:</b>&nbsp;<input type=text id="input_height" value="0"></input>m<br>
<b>Location:</b>&nbsp;<input type=text id="input_location"></input><br>
<b>Band:</b>&nbsp;
<select id="input_band">
  <option value="2m">2m</option>
  <option value="70cm">70cm</option>
  <option value="23cm">23cm</option>
  <option value="13cm">13cm</option>
  <option value="9cm">9cm</option>
  <option value="6cm">6cm</option>
  <option value="3cm">3cm</option>
</select><br>
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
