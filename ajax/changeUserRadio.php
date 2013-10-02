<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = isset($_REQUEST["radio_active"]);
if($got_cookies && $got_variables) {
	require_once('dxspottv_pdo.php');
	$sessions_statement = $dbc->prepare("SELECT session_id FROM sessions WHERE user_id=?;");
	$sessions_statement->bind_param('i', $_COOKIE["user_id"]);
	$sessions_statement->execute();
	$sessions_statement->bind_result($sessions_result);
	$sessions_statement->store_result();
	if($sessions_statement->num_rows==0) { // session doesn't exist on server
		print 'Session not found.';
	} else {
		while ($sessions_statement->fetch()) {
			if ($_COOKIE["session_key"]==$sessions_result) {
				// Session matches, so is logged in!
				$update_statement = $dbc->prepare("UPDATE users set radio_active=? WHERE id=?;");
				$update_statement->bind_param('ii', $_REQUEST["radio_active"], $_COOKIE["user_id"]);
				$update_statement->execute();
				$update_statement->close();
			}
		}
	}
	$sessions_statement->close();
} else { // Not got cookies
	print 'Access Denied.';
}
?>
