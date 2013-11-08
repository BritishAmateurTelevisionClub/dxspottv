<?php
session_start();
if(isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"])) {
	require_once('dxspottv_pdo.php');
	$sessions_statement = $dbc->prepare("SELECT session_id FROM sessions WHERE user_id=? ORDER BY activity DESC LIMIT 1;");
	$sessions_statement->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
	$sessions_statement->execute();
	$sessions_statement->bindColumn(1, $sessions_result);
	if($sessions_statement->rowCount()==0) { // session doesn't exist on server
		print 'Session not found.';
	} else {
		while ($sessions_statement->fetch()) {
			if ($_COOKIE["session_key"]==$sessions_result) { // Session matches, so is logged in!
				$update_statement = $dbc->prepare("UPDATE sessions set activity=NOW() where session_id=?;");
				$update_statement->bindValue(1, $_COOKIE["session_key"], PDO::PARAM_STR);
				$update_statement->execute();
			}
		}
	}
} else { // Not got cookies
	print 'Access Denied.';
}
?>
