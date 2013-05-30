<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = (isset($_REQUEST["description"]) && isset($_REQUEST["website"]));
if($got_cookies && $got_variables) {
	$desc = htmlentities($_REQUEST["description"]);
	$website = htmlentities($_REQUEST["website"]);
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
			$update_statement = $dbc->prepare("UPDATE users set station_desc=?,website=? WHERE id=?;");
			$update_statement->bind_param('ssi', $desc, $website, $_COOKIE["user_id"]);
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
