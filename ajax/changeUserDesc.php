<?php
session_start();

$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = (isset($_REQUEST["description"]) && isset($_REQUEST["website"]) && isset($_REQUEST["lat"]) && isset($_REQUEST["lon"]) && isset($_REQUEST["loc"]));
if($got_cookies && $got_variables)
{
    require_once('../dxspottv_pdo.php');
    $sessions_statement = $dbc->prepare("SELECT session_id FROM sessions WHERE user_id=?;");
    $sessions_statement->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
    $sessions_statement->execute();
    $sessions_statement->bindColumn(1, $sessions_result);
    if($sessions_statement->rowCount()==0)
    { 
        print 'Session not found.';
    } 
    else 
    {
        while ($sessions_statement->fetch())
        {
            if ($_COOKIE["session_key"]==$sessions_result)
            {
                $update_statement = $dbc->prepare("UPDATE users set lat=?,lon=?,locator=?,station_desc=?,website=? WHERE id=?;");
                $update_statement->bindValue(1, $_REQUEST["lat"]);
                $update_statement->bindValue(2, $_REQUEST["lon"]);
                $update_statement->bindValue(3, $_REQUEST["loc"], PDO::PARAM_STR);
                $update_statement->bindValue(4, htmlentities($_REQUEST["description"]), PDO::PARAM_STR);
                $update_statement->bindValue(5, htmlentities($_REQUEST["website"]), PDO::PARAM_STR);
                $update_statement->bindValue(6, $_COOKIE["user_id"], PDO::PARAM_INT);
                $update_statement->execute();
            }
        }
    }
}
else
{
    print 'Access Denied / Bad Request.';
}
?>
