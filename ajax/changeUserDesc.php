<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = (isset($_REQUEST["description"]) && isset($_REQUEST["website"]) && isset($_REQUEST["lat"]) && isset($_REQUEST["lon"]) && isset($_REQUEST["loc"]));
if($got_cookies && $got_variables) {
	$desc = htmlentities($_REQUEST["description"]);
	$website = htmlentities($_REQUEST["website"]);
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
				$update_statement = $dbc->prepare("UPDATE users set lat=?,lon=?,locator=?,station_desc=?,website=? WHERE id=?;");
				$update_statement->bindValue(1, $_REQUEST["lat"]); // Dont know datatype, let PDO deal with it
				$update_statement->bindValue(2, $_REQUEST["lon"]);
				$update_statement->bindValue(3, $_REQUEST["loc"], PDO::PARAM_STR);
				$update_statement->bindValue(4, $desc, PDO::PARAM_STR);
				$update_statement->bindValue(5, $website, PDO::PARAM_STR);
				$update_statement->bindValue(6, $_COOKIE["user_id"], PDO::PARAM_INT);
				$update_statement->execute();
			}
		}
	}
} else { // Not got cookies
	print 'Access Denied.';
}
?>
