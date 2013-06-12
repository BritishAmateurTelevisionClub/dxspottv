<?php
session_start();
include('spot_login.php');

if(isset($_REQUEST['callsign'])&&isset($_REQUEST['passwd'])) { // Start Login

require_once('login_functions.php');

$callsign = escape($dbc, strtoupper($_REQUEST['callsign']));
$passwd = escape($dbc, $_REQUEST['passwd']);

$user_id_result = mysqli_query($dbc, "SELECT id,salt,password FROM users WHERE callsign='" . $callsign . "';") or die(mysqli_error($dbc));
$target_row = mysqli_fetch_array($user_id_result);
$user_id = $target_row['id'];
$target = $target_row['password'];
$salt = $target_row['salt'];
$crypt = crypt($passwd, $salt);

if($crypt==$target) {
	$session_key = sha256_salt();
	
	// Ugly hack, set radio_active to true on login
	$radio_active = 1;
	$update_statement = $dbc->prepare("UPDATE users set radio_active=? WHERE id=?;");
	$update_statement->bind_param('ii', $radio_active, $user_id);
	$update_statement->execute();
	$update_statement->close();

	$insert_query="INSERT into sessions (session_id, user_id) VALUES ('{$session_key}', '{$user_id}');";
	$ret = mysqli_query($dbc, $insert_query) or die(mysqli_error($dbc));

    $return_data = array('error' => 0, 'callsign' => $callsign, 'session_key' => $session_key); 
	setcookie("user_id", $user_id, time()+3600000);
	setcookie("session_key", $session_key, time()+3600000);
	setcookie("auth_error", "0", time()+3600000);
} else {
        $return_data = array('error' => 1, 'message' => 'Login Failed');
	setcookie("auth_error", "1", time()+3600000);
	setcookie("auth_error_text", "Login Failed", time()+3600000);
}
mysql_end($dbc);
//echo json_encode($return_data);
} // End Login
header( 'Location: http://www.dxspot.tv/' ) 
?>
