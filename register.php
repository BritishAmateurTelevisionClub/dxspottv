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
<table width=100%>
<tr width=100%>
<td width=50%>
<h2>New User Registration</h2>
Please enter the following information to register for DXSpot.TV<br>
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
<br>
<label class="register_labels"><b>Locator:</b>&nbsp;</label><input type=text name='locator' id='locator' class="required" minlength="6" /> Maidenhead QRA eg. IO91HW (Use 6 characters please)
<br>
<label class="register_labels"><b>Email Address:</b>&nbsp;</label><input type=text name='email' id='email' class="required email" /> Will only be used for administrator contact in case of issues. It will not be publicly disclosed.
<br><br>
<?php
require_once('recaptchalib.php');
$publickey = "6LfVM-ESAAAAAIFKeTo0dbqWVOu7c4nd-epDy4qk";
echo recaptcha_get_html($publickey);
?>
</td>
<td width=50%>
<center>
<h3>Station Location</h3>
Click to set location, as accurate as you like.
<div id="map_canvas"></div>
<br>
<label class="register_labels"><b>Latitude:</b>&nbsp;</label><input type=text name='lat' id='lat' class="required number" />
<br>
<label class="register_labels"><b>Longitude:</b>&nbsp;</label><input type=text name='lon' id='lon' class="required number" />
</center>
</td></tr></table>
<center>
<button id="register_button">Register</button>
</center>
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
