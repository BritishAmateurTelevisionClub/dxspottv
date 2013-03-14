<?php
function sha256_salt() {
	$base64_alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ'
	   .'abcdefghijklmnopqrstuvwxyz0123456789+/';
   	$salt='$5$rounds=1667$';
    	for($i=0; $i<13; $i++){
        	$salt.=$base64_alphabet[mt_rand(0,63)];
    	}
	$salt.='$';
	return $salt;
}
?>