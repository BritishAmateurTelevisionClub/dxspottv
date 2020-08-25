<?php
session_start();
header('Content-Type: application/json');

$rl_count = apc_fetch('dxspot_login_rl',$rl_exists);
if($rl_exists && $rl_count >= 10)
{
   print(json_encode(array('error' => 1, 'message' => 'Too many failed attempts, please wait a few seconds.')));
   exit();
}

$return_data = array();
if(isset($_REQUEST['callsign']) && isset($_REQUEST['passwd']))
{
    include('../dxspottv_pdo.php');
    $user_statement = $dbc->prepare("SELECT id,salt,callsign,password FROM users WHERE callsign=?;");
    $user_statement->bindValue(1, escape(strtoupper($_REQUEST['callsign'])), PDO::PARAM_STR);
    $user_statement->execute();
    $user_statement->bindColumn(1, $user_id);
    $user_statement->bindColumn(2, $salt);
    $user_statement->bindColumn(3, $callsign);
    $user_statement->bindColumn(4, $target);
    $user_statement->fetch();

    if($user_statement->rowCount()==1)
    {
        if($target == crypt(escape($_REQUEST['passwd']), $salt))
        {
            $oldsessions_statement = $dbc->prepare("DELETE FROM sessions WHERE user_id=?;");
            $oldsessions_statement->bindValue(1, $user_id, PDO::PARAM_INT);
            $oldsessions_statement->execute();

            $session_key = sha256_salt();
            $session_statement = $dbc->prepare("INSERT into sessions (session_id, user_id) VALUES (?,?);");
            $session_statement->bindValue(1, $session_key, PDO::PARAM_STR);
            $session_statement->bindValue(2, $user_id, PDO::PARAM_INT);
            $session_statement->execute();

            $return_data = array('error' => 0, 'callsign' => $callsign, 'session_key' => $session_key); 
            setcookie("user_id", $user_id, time()+3600000, "/");
            setcookie("session_key", $session_key, time()+3600000, "/");
            setcookie("auth_error", "0", time()+3600000, "/");
        }
        else
        {
            $return_data = array('error' => 1, 'message' => 'Incorrect Callsign or Password');
            #setcookie("auth_error", "1", time()+3600000, "/");
            #setcookie("auth_error_text", "Login Failed", time()+3600000, "/");
        }
    }
    else
    {
        $return_data = array('error' => 1, 'message' => 'Incorrect Callsign or Password');
        #setcookie("auth_error", "1", time()+3600000, "/");
        #setcookie("auth_error_text", "Login Failed", time()+3600000, "/");
    }
}

if($return_data['error']==1)
{
    apc_inc('dxspot_login_rl',1,$success);
    if($success==false)
    {
        apc_store('dxspot_login_rl',1,5);
    }
}
print json_encode($return_data);
?>
