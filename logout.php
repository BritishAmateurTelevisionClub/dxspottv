<?php
session_start();

setcookie("user_id", "", time()-3200);
setcookie("session_key", "", time()-3200);
setcookie("auth_error", "1", time()+3200);
setcookie("auth_error_text", "Logged Out. Please Log in again.", time()+3200);

header( 'Location: https://www.thecraag.com/atvspot/' ) 
?>
