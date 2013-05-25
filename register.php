<?php
session_start();
?>
<html>
<head>
<title>DXSpot.TV - Register</title>
<link href="/css/atvspot-register.css" rel="stylesheet">
<link href="css/flick/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script src="/js/jquery-plus-ui.js"></script>
<script src="/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/js/register.js"></script>
<script type="text/javascript" src="/js/locator.js"></script>
</head>
<body>
<div id="first_form">
<h2>New User Registration</h2><br>
Please enter the following information to register for ATV DxSpot<br>
<br>
<form id='register_form'>
	<label class="register_labels"><b>First Name:</b>&nbsp;</label>
	<input type=text name='fname' id='fname' class="required" minlength="2" />
<br>
	<label class="register_labels"><b>Callsign:</b>&nbsp;</label>
	<input type=text name='callsign' id='callsign' class="required" minlength="4" /> Will be converted to Upper case. eg. M0DNY
<br>
	<label class="register_labels"><b>Password:</b>&nbsp;</label>
	<input type=password name='passwd' id='passwd' class="required" minlength="5" />
<br>
<br>Entering your lat and long or a 6 digit QRA will give greater location accuracy<br>
<br>
<label class="register_labels"><b>Locator:</b>&nbsp;</label><input type=text name='locator' id='locator' onChange=calc_lat_lon() class="required" minlength="4" /> Maidenhead QRA eg. IO91HW (Use either 4 or 6 characters)
<br>
<label class="register_labels"><b>Latitude:</b>&nbsp;</label><input type=text name='lat' id='lat' class="required number" /> (Will fill in automatically if QRA locator entered)
<br>
<label class="register_labels"><b>Longitude:</b>&nbsp;</label><input type=text name='lon' id='lon' class="required number" /> (Will fill in automatically if QRA locator entered)
<br>
<label class="register_labels"><b>Email Address:</b>&nbsp;</label><input type=text name='email' id='email' class="required email" /> Will only be used for administrator contact in case of issues. It will not be publicly disclosed.
<br><br>
<?php
require_once('recaptchalib.php');
$publickey = "6LfVM-ESAAAAAIFKeTo0dbqWVOu7c4nd-epDy4qk";
echo recaptcha_get_html($publickey);
?>
<button id="register_button">Register</button>
</form>
</div>
<div id="successMessage" style="display: none">
<h2>Registration Successful!</h2>
<br>
<button class="reduce-font-size" id="return_button">Return to Home</button>
</div>
<div id="validationFailDialog" title="Validation Failed">The Form failed validation, please check and try again.</div>
<div id="captchaFailDialog" title="Captcha Failed">The Captcha was incorrect, please try again.</div>
</body>
</html>
