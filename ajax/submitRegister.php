<?php

$got_variables = (isset($_REQUEST["fname"]) && isset($_REQUEST["callsign"]) && isset($_REQUEST["passwd"]) && isset($_REQUEST["email"]) && isset($_REQUEST["locator"]) && isset($_REQUEST["lat"]) && isset($_REQUEST["lon"]));

if($got_variables) {
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

	require_once("login_functions.php");
	require_once("spot_login.php");
	
	$callsign = htmlentities(strtoupper($_REQUEST["callsign"]));
	$passwd = $_REQUEST["passwd"];
	$email = htmlentities($_REQUEST["email"]);
	$locator = htmlentities(strtoupper($_REQUEST["locator"]));
	$lat = htmlentities($_REQUEST["lat"]);
	$lon = htmlentities($_REQUEST["lon"]);
	$name = htmlentities($_REQUEST["fname"]);
	
	$existing_statement = $dbc->prepare("SELECT id FROM users WHERE callsign=?;");
	$existing_statement->bind_param('s', $callsign);
	$existing_statement->execute();
	$existing_statement->bind_result($sessions_result);
	$existing_statement->store_result();
	
	if($existing_statement->num_rows>0) { // Existing User!
		$output['successful'] = 0;
		$output['error'] = "3";
		print json_encode($output);
		die ();
	}
	
	$insert_statement = $dbc->prepare("INSERT into users (name, callsign, password, salt, locator, email, lat, lon) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");

	$salt = sha256_salt();

	$crypt = crypt($passwd, $salt);
	
	$insert_statement->bind_param('ssssssdd', $name, $callsign, $crypt, $salt, $locator, $email, $lat, $lon);

	$insert_statement->execute();
	
	if($insert_statement->affected_rows==1) {
		$output['successful'] = 1;
	} else {
		$output['successful'] = 0;
		$output['error'] = "2"; // MYSQL Error
	}
} else {
	$output['successful'] = 0;
	$output['error'] = "0"; // Lack of stuff Error
}
print json_encode($output);
?>
