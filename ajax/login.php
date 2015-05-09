<?php
session_start();
include('../dxspottv_pdo.php');

if(isset($_REQUEST['callsign'])&&isset($_REQUEST['passwd'])) { // Start Login

$callsign = escape(strtoupper($_REQUEST['callsign']));
$passwd = escape($_REQUEST['passwd']);

$user_statement = $dbc->prepare("SELECT id,salt,password FROM users WHERE callsign=?;");
$user_statement->bindValue(1, $callsign, PDO::PARAM_STR);
$user_statement->execute();
$user_statement->bindColumn(1, $user_id);
$user_statement->bindColumn(2, $salt);
$user_statement->bindColumn(3, $target);
$user_statement->fetch();
$crypt = crypt($passwd, $salt);

if($crypt==$target) {
	$session_key = sha256_salt();

	$session_statement = $dbc->prepare("INSERT into sessions (session_id, user_id) VALUES (?,?);");
	$session_statement->bindValue(1, $session_key, PDO::PARAM_STR);
	$session_statement->bindValue(2, $user_id, PDO::PARAM_INT);
	$session_statement->execute();

    $return_data = array('error' => 0, 'callsign' => $callsign, 'session_key' => $session_key);
} else {
    $return_data = array('error' => 1, 'message' => 'Login Failed');
}
echo json_encode($return_data);
}
?>
