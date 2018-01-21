<?php
//header('Content-Type: application/json');
require_once('../dxspottv_pdo.php');

if(!(isset($_REQUEST["key"]) && isset($_REQUEST["passwd"])))
{
    die("Reset key and/or password not provided");
}
$resetKey = $_REQUEST["key"];
$passwd = $_REQUEST["passwd"];

$key_stmt = $dbc->prepare("SELECT id from users WHERE resetKey=?;");
$key_stmt->bindValue(1, $resetKey, PDO::PARAM_STR);
$key_stmt->execute();
$key_stmt->bindColumn(1, $user_id);
if($key_stmt->rowCount()!=1) {
    die("Reset key does not exist");
}
$key_stmt->fetch();

$salt = sha256_salt();
$crypt = crypt($passwd, $salt);

$update_stmt = $dbc->prepare("UPDATE users SET password=?, salt=?, resetKey=NULL WHERE id=?;");;
$update_stmt->bindValue(1, $crypt, PDO::PARAM_STR);
$update_stmt->bindValue(2, $salt, PDO::PARAM_STR);
$update_stmt->bindValue(3, $user_id, PDO::PARAM_INT);
$update_stmt->execute();

print("Done.");
?>
