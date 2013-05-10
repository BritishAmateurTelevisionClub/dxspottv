<?php
session_start();
?>
<html>
<head>
<title>DXSpot.TV - Register</title>
<link href="/css/atvspot-register.css" rel="stylesheet">
<link href="css/flick/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script src="/js/jquery-1.9.1.min.js"></script>
<script src="/js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="/js/jquery.validate.js"></script>
<script type="text/javascript" src="/js/register.js"></script>
<script type="text/javascript" src="/js/locator.js"></script>
</head>
<body>
<h2>New User Registration</h2>
<form id='register_form' action='/register.php' method="post">
	<label class="register_labels"><b>First Name/Nickname:</b>&nbsp;</label>
	<input type=text name='fname' class="required" minlength="2" />
<br>
	<label class="register_labels"><b>Callsign:</b>&nbsp;</label>
	<input type=text name='callsign' class="required" minlength="4" /> Will be converted to Upper case. eg. M0DNY
<br>
	<label class="register_labels"><b>Password:</b>&nbsp;</label>
	<input type=password name='passwd' class="required" minlength="5" />
<br><br>
<label class="register_labels"><b>Locator:</b>&nbsp;</label><input type=text name='locator' id='locator' onChange=calc_lat_lon() class="required" minlength="4" /> Maidenhead eg. IO91HW (Use either 4 or 6 characters)
<br>
<label class="register_labels"><b>Latitude:</b>&nbsp;</label><input type=text name='lat' id='lat' />
<br>
<label class="register_labels"><b>Longitude:</b>&nbsp;</label><input type=text name='lon' id='lon' /> (Will fill in automatically from Locator)
<br>
<label class="register_labels"><b>Email Address:</b>&nbsp;</label><input type=text name='email' class="required email" /> Just for administrator contact in case of issues. Not publicly disclosed.
<br><br>
<?php
require_once('recaptchalib.php');
$publickey = "6LfVM-ESAAAAAIFKeTo0dbqWVOu7c4nd-epDy4qk";
echo recaptcha_get_html($publickey);
?>
<br>
<button class="reduce-font-size" id="register_button">Register</button>
</form>
<h2>Registration Successful!</h2>
<br>
Click <a href="/">here</a> to return to the map and log in.
</body>
</html>
