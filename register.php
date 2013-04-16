<html>
<head>
<title>Register New User</title>
<script type="text/javascript" src="js/locator.js"></script>
<script>
function calc_lat_lon() {
	var latlon = [];
	latlon = LoctoLatLon(document.getElementById("locator").value);
	document.getElementById("lat").value = latlon[0];
	document.getElementById("lon").value = latlon[1];
}
</script>
</head>
<body>
<?php
session_start();

if(!(isset($_REQUEST["callsign"]) && isset($_REQUEST["passwd"]))) {
?>
<h2>New User Registration</h2>
<form action='/atvspot/register.php' method="post">
<table style='padding: 5px;'>
<tr><td>
<b>Callsign:</b>&nbsp;
</td><td>
<input type=text name='callsign' /> Will be converted to Upper case. eg. M0DNY
</td></tr>
<tr><td>
<b>Password:</b>&nbsp;
</td><td>
<input type=password name='passwd' />
</td></tr>
<tr></tr>
<tr><td>
<b>Locator:</b>&nbsp;
</td><td>
<input type=text name='locator' id='locator' onChange=calc_lat_lon() /> Maidenhead eg. IO91HW (Use either 4 or 6 characters)
</td></tr>
<tr>
<td>
<b>Latitude:</b>&nbsp;
</td><td>
<input type=text name='lat' id='lat' />
<b>Longitude:</b>&nbsp;
<input type=text name='lon' id='lon' /> (Will fill in automatically from Locator)
</td>
</tr>
<tr><td>
<b>Email Address:</b>&nbsp;
</td><td>
<input type=text name='email' /> Just for administrator contact in case of issues. Not publicly disclosed.
</td></tr>
</table>
<br>
<?php
require_once('recaptchalib.php');
$publickey = "6LfceNsSAAAAAFQpBQ9euTLv5ZEcNIjx6u0vj63B";
echo recaptcha_get_html($publickey);
?>
<br>
<input type=submit value='Register' />
</form>
<?php
} else {

require_once('recaptchalib.php');
$privatekey = "6LfceNsSAAAAAOX9XoasUeOg6uMA0pYSPYpIn3AY";
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
	die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
         "(reCAPTCHA said: " . $resp->error . ")");
}

include_once("login_functions.php");
include_once("spot_login.php");

$callsign = escape($dbc, strtoupper($_REQUEST["callsign"]));
$passwd = escape($dbc, $_REQUEST["passwd"]);
$locator = escape($dbc, $_REQUEST["locator"]);
$email = escape($dbc, $_REQUEST["email"]);
$lat = escape($dbc, $_REQUEST["lat"]);
$lon = escape($dbc, $_REQUEST["lon"]);

$salt = sha256_salt();

$crypt = crypt($passwd, $salt);

$insert_query="INSERT into users (callsign, password, salt, locator, email, lat, lon) VALUES ('{$callsign}', '{$crypt}', '{$salt}', '{$locator}', '{$email}', '{$lat}', '{$lon}');";
$ret = mysqli_query($dbc, $insert_query) or die(mysqli_error($dbc));
?>
<h2>Registration Successful!</h2>
<br>
Click <a href="/atvspot/">here</a> to return to the map and log in.

<?php
mysql_end($dbc);
}
?>
</body>
</html>
