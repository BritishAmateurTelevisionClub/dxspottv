<?php
session_start();

if(isset($_REQUEST["fname"]) && isset($_REQUEST["callsign"]) && isset($_REQUEST["passwd"]) && isset($_REQUEST["email"]) && isset($_REQUEST["locator"]) && isset($_REQUEST["lat"]) && isset($_REQUEST["lon"]))
{
    require_once('reCAPTCHA.php');
    $reCAPTCHA = new reCAPTCHA('6LcXvUEUAAAAAHrEskwoASn4Q2hkYCRSjtlk4dJs','6LcXvUEUAAAAAGL-LV2GkH9kzSShJiW65tzPLZ_a');

    $output = array();
    if (!$reCAPTCHA->isValid($_REQUEST['recaptcha']))
    {
        $output['successful'] = 0;
        $output['error'] = 1; // CAPTCHA Error
        print json_encode($output);
        die ();
    }

    require_once("../dxspottv_pdo.php");
    
    $callsign = htmlentities(strtoupper($_REQUEST["callsign"]));
    $passwd = $_REQUEST["passwd"];
    $email = htmlentities($_REQUEST["email"]);
    $locator = htmlentities(strtoupper($_REQUEST["locator"]));
    $lat = htmlentities($_REQUEST["lat"]);
    $lon = htmlentities($_REQUEST["lon"]);
    $name = htmlentities($_REQUEST["fname"]);
    
    $existing_statement = $dbc->prepare("SELECT id,known FROM users WHERE callsign=?;");
    $existing_statement->bindValue(1, $callsign, PDO::PARAM_STR);
    $existing_statement->execute();
    $existing_statement->bindColumn(1, $existing_id);
    $existing_statement->bindColumn(2, $existing_result);
    
    $salt = sha256_salt();
    $crypt = crypt($passwd, $salt);
    
    if($existing_statement->rowCount()>0)
    { // Existing User!
        $existing_statement->fetch();
        if($existing_result==1)
        { //Existing real user
            $output['successful'] = 0;
            $output['error'] = 3;
            print json_encode($output);
            die ();
        }
        else
        { // User was unknown previously
            $known = 1;
            $insert_statement = $dbc->prepare("UPDATE users SET name=?, callsign=?, password=?, salt=?, locator=?, email=?, lat=?, lon=?, known=? WHERE id=?;");
            $insert_statement->bindValue(1, $name, PDO::PARAM_STR);
            $insert_statement->bindValue(2, $callsign, PDO::PARAM_STR);
            $insert_statement->bindValue(3, $crypt, PDO::PARAM_STR);
            $insert_statement->bindValue(4, $salt, PDO::PARAM_STR);
            $insert_statement->bindValue(5, $locator, PDO::PARAM_STR);
            $insert_statement->bindValue(6, $email, PDO::PARAM_STR);
            $insert_statement->bindValue(7, $lat);
            $insert_statement->bindValue(8, $lon);
            $insert_statement->bindValue(9, $known, PDO::PARAM_INT);
            $insert_statement->bindValue(10, $existing_id, PDO::PARAM_INT);
            $insert_statement->execute();
        }
    }
    else
    {
        $insert_statement = $dbc->prepare("INSERT into users (name, callsign, password, salt, locator, email, lat, lon) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
        $insert_statement->bindValue(1, $name, PDO::PARAM_STR);
        $insert_statement->bindValue(2, $callsign, PDO::PARAM_STR);
        $insert_statement->bindValue(3, $crypt, PDO::PARAM_STR);
        $insert_statement->bindValue(4, $salt, PDO::PARAM_STR);
        $insert_statement->bindValue(5, $locator, PDO::PARAM_STR);
        $insert_statement->bindValue(6, $email, PDO::PARAM_STR);
        $insert_statement->bindValue(7, $lat);
        $insert_statement->bindValue(8, $lon);
        $insert_statement->execute();
    }
    
    if($insert_statement->rowCount()==1)
    {
        $output['successful'] = 1;
    }
    else
    {
        $output['successful'] = 0;
        $output['error'] = 2; // MYSQL Error
    }
}
else
{
    $output['successful'] = 0;
    $output['error'] = 0; // Lack of stuff Error
}
print json_encode($output);
?>
