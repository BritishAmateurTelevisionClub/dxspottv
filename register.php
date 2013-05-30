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
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-37815608-6', 'dxspot.tv');
  ga('send', 'pageview');
</script>
</head>
<body>
<div id="first_form">
<form id='register_form'>
<table width=100%>
<tr width=100%>
<td style="width: auto;">
<h2>New User Registration</h2>
Please enter the all following information and use the map to set your location<br>
<br>
	<label class="register_labels"><b>First Name:</b>&nbsp;</label>
	<input type=text name='fname' id='fname' class="required" minlength="2" /> (Your chatroom name will be <i>firstname_callsign</i>)
<br><br>
	<label class="register_labels"><b>Callsign:</b>&nbsp;</label>
	<input type=text name='callsign' id='callsign' class="required" minlength="4" /> Will be converted to Upper case. eg. M0DNY
<br><br>
	<label class="register_labels"><b>Password:</b>&nbsp;</label>
	<input type=password name='passwd' id='passwd' class="required" minlength="5" /> (minimum 5 characters)
<br><br>
<label class="register_labels"><b>Email Address:</b>&nbsp;</label><input type=text name='email' id='email' class="required email" />
<li>Will only be used for administrator contact in case of issues. Will not be publicly disclosed.</li>
<br>
<br>
<br>
Once you have entered the information and used the map to set your location,<br> please complete the captcha and press register<br>
<br>
<?php
require_once('recaptchalib.php');
$publickey = "6LfVM-ESAAAAAIFKeTo0dbqWVOu7c4nd-epDy4qk";
echo recaptcha_get_html($publickey);
?>
</td>
<td style="width: 450px;">
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
</html>
