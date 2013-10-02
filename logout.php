<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));

if($got_cookies) {
	require_once('dxspottv_login.php');
	$logout_statement = $dbc->prepare("DELETE FROM sessions WHERE session_id=? LIMIT 1;");
	$logout_statement->bind_param('s', $_COOKIE["session_key"]);
	$logout_statement->execute();
}
	
setcookie("user_id", "", time()-3200);
setcookie("session_key", "", time()-3200);
setcookie("auth_error", "1", time()+3200);
setcookie("auth_error_text", "Logged Out. Please Log in again.", time()+3200);

header( 'Location: http://www.dxspot.tv/' ) 
?>
