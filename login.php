<?php
session_start();
include('dxspottv_pdo.php');

if(isset($_REQUEST['callsign'])&&isset($_REQUEST['passwd'])) { // Start Login

$callsign = escape(strtoupper($_REQUEST['callsign']));
$passwd = escape($_REQUEST['passwd']);

$user_statement = $dbc->prepare("SELECT id,salt,password FROM users WHERE callsign=?;");
$user_statement->bind_param('i', $callsign);
$user_statement->execute();
$user_statement->bind_result($user_id, $salt, $target);
$user_statement->fetch();
$user_statement->close();
$crypt = crypt($passwd, $salt);

if($crypt==$target) {
	$session_key = sha256_salt();
	
	// Ugly hack, set radio_active to true on login
	$radio_active = 1;
	$update_statement = $dbc->prepare("UPDATE users set radio_active=? WHERE id=?;");
	$update_statement->bind_param('ii', $radio_active, $user_id);
	$update_statement->execute();
	$update_statement->close();

	$session_statement = $dbc->prepare("INSERT into sessions (session_id, user_id) VALUES (?,?);");
	$session_statement->bind_param('ii', $session_key, $user_id);
	$session_statement->execute();
	$session_statement->close();

   $return_data = array('error' => 0, 'callsign' => $callsign, 'session_key' => $session_key); 
	setcookie("user_id", $user_id, time()+3600000);
	setcookie("session_key", $session_key, time()+3600000);
	setcookie("auth_error", "0", time()+3600000);
} else {
        $return_data = array('error' => 1, 'message' => 'Login Failed');
	setcookie("auth_error", "1", time()+3600000);
	setcookie("auth_error_text", "Login Failed", time()+3600000);
}
//echo json_encode($return_data);
} // End Login
header( 'Location: http://www.dxspot.tv/' ) 
?>
