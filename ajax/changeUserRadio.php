<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = isset($_REQUEST["radio_active"]);
if($got_cookies && $got_variables) {
	require_once('../dxspottv_pdo.php');
	$sessions_statement = $dbc->prepare("SELECT session_id FROM sessions WHERE user_id=?;");
	$sessions_statement->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
	$sessions_statement->execute();
	$sessions_statement->bindColumn(1, $sessions_result);
	if($sessions_statement->rowCount()==0) { // session doesn't exist on server
		print 'Session not found.';
	} else {
		while ($sessions_statement->fetch()) {
			if ($_COOKIE["session_key"]==$sessions_result) { // Session matches, so is logged in!
				$update_statement = $dbc->prepare("UPDATE users set radio_active=? WHERE id=?;");
				$update_statement->bindValue(1, $_REQUEST["radio_active"], PDO::PARAM_INT);
				$update_statement->bindValue(2, $_COOKIE["user_id"], PDO::PARAM_INT);
				$update_statement->execute();
			}
		}
	}
} else { // Not got cookies
	print 'Access Denied.';
}
?>
