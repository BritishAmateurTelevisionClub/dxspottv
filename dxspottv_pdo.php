<?php

include('credentials.php');

$dbc = new PDO(
 'mysql:'.
 'host=' . $mysql_credentials['host'] . ';'.
 'dbname=' . $mysql_credentials['database'],
 $mysql_credentials['user'],
 $mysql_credentials['password'],
 array(
  PDO::ATTR_PERSISTENT => true
 )
);

function escape($str) {
	return addcslashes($str, '%_');
}

function sha256_salt() {
	$base64_alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	$salt='$5$rounds=1667$';
 	for($i=0; $i<13; $i++){
     	$salt.=$base64_alphabet[mt_rand(0,63)];
 	}
	$salt.='$';
	return $salt;
}
function random_alphanumeric(int $length) {
    $base62_alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $salt='';
    for($i=0; $i<$length; $i++){
    $salt.=$base62_alphabet[mt_rand(0,61)];
    }
    return $salt;
}
?>
