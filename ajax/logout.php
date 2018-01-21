<?php
session_start();

if(isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]))
{
    require_once('../dxspottv_pdo.php');
    $logout_statement = $dbc->prepare("DELETE FROM sessions WHERE session_id=? LIMIT 1;");
    $logout_statement->bindValue(1, $_COOKIE["session_key"], PDO::PARAM_STR);
    $logout_statement->execute();
}
    
setcookie("user_id", "", time()-3200, "/");
setcookie("session_key", "", time()-3200, "/");
setcookie("auth_error", "1", time()+3200, "/");
setcookie("auth_error_text", "Logged Out. Please Log in again.", time()+3200, "/");

header( 'Location: https://www.dxspot.tv/' ) 
?>
