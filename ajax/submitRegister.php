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

	require_once("dxspottv_pdo.php");
	
	$callsign = htmlentities(strtoupper($_REQUEST["callsign"]));
	$passwd = $_REQUEST["passwd"];
	$email = htmlentities($_REQUEST["email"]);
	$locator = htmlentities(strtoupper($_REQUEST["locator"]));
	$lat = htmlentities($_REQUEST["lat"]);
	$lon = htmlentities($_REQUEST["lon"]);
	$name = htmlentities($_REQUEST["fname"]);
	
	$existing_statement = $dbc->prepare("SELECT id,known FROM users WHERE callsign=?;");
	$existing_statement->bindValue(1, $callsign, PDO::PARAM_STR);
	$existing_statement->execute();
	$existing_statement->bindColumn(1, $existing_id);
	$existing_statement->bindColumn(2, $existing_result);
	
	$salt = sha256_salt();
	$crypt = crypt($passwd, $salt);
	
	if($existing_statement->rowCount()>0) { // Existing User!
		$existing_statement->fetch();
		if($existing_result==1) { //Existing real user
			$output['successful'] = 0;
			$output['error'] = "3";
			print json_encode($output);
			die ();
		} else { // User was unknown previously
			$known = 1;
			$insert_statement = $dbc->prepare("UPDATE users SET name=?, callsign=?, password=?, salt=?, locator=?, email=?, lat=?, lon=?, known=? WHERE id=?;");
			$insert_statement->bind_param('ssssssdddd', $name, $callsign, $crypt, $salt, $locator, $email, $lat, $lon, $known, $existing_id);
			$insert_statement->bindValue(1, $name, PDO::PARAM_STR);
			$insert_statement->bindValue(2, $callsign, PDO::PARAM_STR);
			$insert_statement->bindValue(3, $crypt, PDO::PARAM_STR);
			$insert_statement->bindValue(4, $salt, PDO::PARAM_STR);
			$insert_statement->bindValue(5, $locator, PDO::PARAM_STR);
			$insert_statement->bindValue(6, $email, PDO::PARAM_STR);
			$insert_statement->bindValue(7, $lat);
			$insert_statement->bindValue(8, $lon);
			$insert_statement->bindValue(9, $known, PDO::PARAM_INT);
			$insert_statement->bindValue(10, $existing_id, PDO::PARAM_INT);
			$insert_statement->execute();
		}
	} else {
		$insert_statement = $dbc->prepare("INSERT into users (name, callsign, password, salt, locator, email, lat, lon) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
		$insert_statement->bind_param('ssssssdd', $name, $callsign, $crypt, $salt, $locator, $email, $lat, $lon);
		$insert_statement->bindValue(1, $name, PDO::PARAM_STR);
		$insert_statement->bindValue(2, $callsign, PDO::PARAM_STR);
		$insert_statement->bindValue(3, $crypt, PDO::PARAM_STR);
		$insert_statement->bindValue(4, $salt, PDO::PARAM_STR);
		$insert_statement->bindValue(5, $locator, PDO::PARAM_STR);
		$insert_statement->bindValue(6, $email, PDO::PARAM_STR);
		$insert_statement->bindValue(7, $lat);
		$insert_statement->bindValue(8, $lon);
		$insert_statement->execute();
	}
	
	if($insert_statement->rowCount()==1) {
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
