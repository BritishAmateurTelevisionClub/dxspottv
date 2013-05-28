<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variable = isset($_REQUEST["description"]);
if($got_cookies && $got_variable) {
	$desc = htmlentities($_REQUEST["description"]);
	require_once('spot_login.php');
	$sessions_statement = $dbc->prepare("SELECT session_id FROM sessions WHERE user_id=?;");
	$sessions_statement->bind_param('i', $_COOKIE["user_id"]);
	$sessions_statement->execute();
	$sessions_statement->bind_result($sessions_result);
	$sessions_statement->store_result();
	if($sessions_statement->num_rows!=1) { // session doesn't exist on server
		print 'Session not found.';
	} else {
		$sessions_statement->fetch();
		if ($_COOKIE["session_key"]==$sessions_result) {
			// Session matches, so is logged in!
			$update_statement = $dbc->prepare("UPDATE users set station_desc=? WHERE id=?;");
			$update_statement->bind_param('si', $desc, $_COOKIE["user_id"]);
			$update_statement->execute();
			$update_statement->close();
		} else {
			print 'Session doesnt match.';
		}
	}
	$sessions_statement->close();
	mysql_end($dbc);
} else { // Not got cookies
	print 'Access Denied.';
}
?>
