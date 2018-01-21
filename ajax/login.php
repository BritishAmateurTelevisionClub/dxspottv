<?php
session_start();

if(isset($_REQUEST['callsign']) && isset($_REQUEST['passwd']))
{
    include('../dxspottv_pdo.php');
    $user_statement = $dbc->prepare("SELECT id,salt,password FROM users WHERE callsign=?;");
    $user_statement->bindValue(1, escape(strtoupper($_REQUEST['callsign'])), PDO::PARAM_STR);
    $user_statement->execute();
    $user_statement->bindColumn(1, $user_id);
    $user_statement->bindColumn(2, $salt);
    $user_statement->bindColumn(3, $target);
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
            $return_data = array('error' => 1, 'message' => 'Login Failed');
            setcookie("auth_error", "1", time()+3600000, "/");
            setcookie("auth_error_text", "Login Failed", time()+3600000, "/");
        }
    }
    else
    {
        $return_data = array('error' => 1, 'message' => 'Login Failed');
        setcookie("auth_error", "1", time()+3600000, "/");
        setcookie("auth_error_text", "Login Failed", time()+3600000, "/");
    }
}
header( 'Location: https://www.dxspot.tv/' ) 
?>
