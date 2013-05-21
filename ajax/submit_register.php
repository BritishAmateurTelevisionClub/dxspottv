<?php

if(!(isset($_REQUEST["fname"]) && isset($_REQUEST["callsign"]) && isset($_REQUEST["passwd"]) && isset($_REQUEST["email"]) && isset($_REQUEST["locator"]) && isset($_REQUEST["lat"]) && isset($_REQUEST["lon"]))) {
	$output['successful'] = 0;
	$output['error'] = "0"; // Lack of stuff Error
} else {

require_once('recaptchalib.php');
$privatekey = "6LfVM-ESAAAAAJa-5SRWpWMBEOI1z1UNSkVbvqzp";
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_REQUEST["recaptcha_challenge_field"],
                                $_REQUEST["recaptcha_response_field"]);

$output = array();

if (!$resp->is_valid) {
	$output['successful'] = 0;
	$output['error'] = "1"; // CAPTCHA Error
	print json_encode($output);
	die ();
}

include_once("login_functions.php");
include_once("spot_login.php");

$callsign = mysqli_real_escape_string($dbc, strtoupper($_REQUEST["callsign"]));
$passwd = mysqli_real_escape_string($dbc, $_REQUEST["passwd"]);
$email = mysqli_real_escape_string($dbc, $_REQUEST["email"]);
$locator = mysqli_real_escape_string($dbc, strtoupper($_REQUEST["locator"]));
$lat = mysqli_real_escape_string($dbc, $_REQUEST["lat"]);
$lon = mysqli_real_escape_string($dbc, $_REQUEST["lon"]);
$name = mysqli_real_escape_string($dbc, $_REQUEST["fname"]);

if($callsign=="") {
	$output['successful'] = 0;
	$output['error'] = "2"; // Callsign Error
} else if($passwd=="") {
	$output['successful'] = 0;
	$output['error'] = "3"; // Password Error
} else if($email=="") {
	$output['successful'] = 0;
	$output['error'] = "4"; // Email Error
} else if($locator=="") {
	$output['successful'] = 0;
	$output['error'] = "5"; // Locator Error
} else if($lat=="") {
	$output['successful'] = 0;
	$output['error'] = "6"; // Lat Error
} else if($lon=="") {
	$output['successful'] = 0;
	$output['error'] = "7"; // Lon Error
} else if($name=="") {
	$output['successful'] = 0;
	$output['error'] = "8"; // Name Error
} else { // No error!
$salt = sha256_salt();

$crypt = crypt($passwd, $salt);

$insert_query="INSERT into users (name, callsign, password, salt, locator, email, lat, lon) VALUES ('{$name}', '{$callsign}', '{$crypt}', '{$salt}', '{$locator}', '{$email}', '{$lat}', '{$lon}');";
$ret = mysqli_query($dbc, $insert_query) or die(mysqli_error($dbc));
$output['successful'] = 1;
}
}
print json_encode($output);
?>
