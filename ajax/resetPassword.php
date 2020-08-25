<?php
session_start();
header('Content-Type: application/json');

$rl_count = apc_fetch('dxspot_resetPassword_rl',$rl_exists);
if($rl_exists && $rl_count >= 10)
{
   print(json_encode(array('error' => 1, 'message' => 'Too many failed attempts, please wait a few seconds.')));
   exit();
}

require_once('../dxspottv_pdo.php');

$return_data = array();
if(isset($_REQUEST["key"]) && isset($_REQUEST["passwd"]))
{
    $key_stmt = $dbc->prepare("SELECT id from users WHERE resetKey=?;");
    $key_stmt->bindValue(1, $_REQUEST["key"], PDO::PARAM_STR);
    $key_stmt->execute();
    $key_stmt->bindColumn(1, $user_id);
    if($key_stmt->rowCount()==1)
    {
        $key_stmt->fetch();
        $salt = sha256_salt();
        $crypt = crypt($_REQUEST["passwd"], $salt);
        
        $update_stmt = $dbc->prepare("UPDATE users SET password=?, salt=?, resetKey=NULL WHERE id=?;");
        $update_stmt->bindValue(1, $crypt, PDO::PARAM_STR);
        $update_stmt->bindValue(2, $salt, PDO::PARAM_STR);
        $update_stmt->bindValue(3, $user_id, PDO::PARAM_INT);
        $update_stmt->execute();
        $return_data = array('error' => 0, 'message' => 'Reset Successful!');
    }
    else
    {
        $return_data = array('error' => 1, 'message' => 'Reset key not found.');
    }
}
else
{
    $return_data = array('error' => 1, 'message' => 'Bad Request');
}

if($return_data['error']==1)
{
    apc_inc('dxspot_resetPassword_rl',1,$success);
    if($success==false)
    {
        apc_store('dxspot_resetPassword_rl',1,5);
    }
}
print(json_encode($return_data));
?>
