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
	
	$existing_statement = $dbc->prepare("SELECT id,known FROM users WHERE callsign=?;");
	$existing_statement->bind_param('s', $callsign);
	$existing_statement->execute();
	$existing_statement->bind_result($existing_id, $existing_result);
	$existing_statement->store_result();
	
	$salt = sha256_salt();
	$crypt = crypt($passwd, $salt);
	
	if($existing_statement->num_rows>0) { // Existing User!
		$existing_statement->fetch();
		if($existing_result==1) { //Existing real user
			$output['successful'] = 0;
			$output['error'] = "3";
			print json_encode($output);
			die ();
		} else { // User was unknown previously
			$insert_statement = $dbc->prepare("UPDATE users SET name=?, callsign=?, password=?, salt=?, locator=?, email=?, lat=?, lon=?, known=? WHERE id=?;");
			$insert_statement->bind_param('ssssssdddd', $name, $callsign, $crypt, $salt, $locator, $email, $lat, $lon, 1, $existing_id);
			$insert_statement->execute();
		}
	} else {
		$insert_statement = $dbc->prepare("INSERT into users (name, callsign, password, salt, locator, email, lat, lon) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
		$insert_statement->bind_param('ssssssdd', $name, $callsign, $crypt, $salt, $locator, $email, $lat, $lon);
		$insert_statement->execute();
	}
	$existing_statement->close();
	
	if($insert_statement->affected_rows==1) {
		$output['successful'] = 1;
	} else {
		$output['successful'] = 0;
		$output['error'] = "2"; // MYSQL Error
	}
	$insert_statement->close();
} else {
	$output['successful'] = 0;
	$output['error'] = "0"; // Lack of stuff Error
}
print json_encode($output);
?>
