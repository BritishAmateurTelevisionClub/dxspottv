<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DXSpot.TV - Register</title>
<link href="/css/atvspot-register.css" rel="stylesheet">
<link href="/lib/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">
</head>
<body>
<div id="first_form">
<form id='register_form'>
<table width=100%>
<tr width=100%>
<td style="width: auto;">
<h2>DXSpot.TV - New User Registration</h2>
Please enter the all following information and use the map to set your location<br>
<b>Please do not register repeaters on this page.</b>
<br><br>
<b>
	<label class="register_labels"><b>First Name:</b>&nbsp;</label>
	<input type=text name='fname' id='fname' class="required" minlength="2" /> (Your chatroom name will be displayed as <i>firstname_callsign</i>)
<br><br>
	<label class="register_labels"><b>Callsign:</b>&nbsp;</label>
	<input type=text name='callsign' id='callsign' class="required" minlength="4" /> Will be converted to Upper case. eg. M0DNY
<br><br>
	<label class="register_labels"><b>Password:</b>&nbsp;</label>
	<input type=password name='passwd' id='passwd' class="required" minlength="5" /> (minimum 5 characters)
        <div id="passwd-bar"></div>
<br><br>
<label class="register_labels"><b>Email Address:</b>&nbsp;</label><input type=text name='email' id='email' class="required email" />
</b>
<li>Will not be publicly disclosed. May be used for Administrator contact in case of issues.</li>
<li>Also may be used for configurable notifications in the future.. <i>Coming Soon(tm)</i>
<br>
<br>
<br>
Once you have entered the information and used the map to set your location,<br> please complete the captcha and press register<br>
<br>
<div class="g-recaptcha" data-sitekey="6LcXvUEUAAAAAHrEskwoASn4Q2hkYCRSjtlk4dJs"></div>
</td>
<td style="width: 450px;">
<center>
<h3>Set Station Location</h3>
<div id="map_canvas"></div>
</center>
<br>
Simply zoom in and click on the map to set your location.<br><br>
<i>Your Latitude and Longitude will be filled in automatically.<i>
<br>
<br>
<label class="register_labels"><b>Latitude:</b>&nbsp;</label><input type=text name='lat' id='lat' class="required number" minlength="4" />
<br>
<label class="register_labels"><b>Longitude:</b>&nbsp;</label><input type=text name='lon' id='lon' class="required number" minlength="4" />
</td></tr></table>
<button id="register_button">Register</button><span id="submitStatus"></span>
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
<script src="/lib/jquery-3.2.1.min.js"></script>
<script src="/lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<script src="/lib/jquery.validate.min.js"></script>
<script src="/js/register.js"></script>
<script src="/js/locator.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA72exT_7wxeWSxakb3hEFVgnWqmv6mx1A&callback=initialize" async defer></script>
<script src="/lib/zxcvbn.js" async></script>
</html>
