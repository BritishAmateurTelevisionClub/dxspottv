<?php
$dbc = new PDO('mysql:host=localhost;dbname=dxspottv', 'dxspottv', 'c4v56ccHvrbVLBBE', array(
    PDO::ATTR_PERSISTENT => true
));

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
?>
