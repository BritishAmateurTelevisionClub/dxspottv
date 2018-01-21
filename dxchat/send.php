<?php

/* Sanity check user input */
if($_POST['room']!="")
{
    $form_room = $_POST['room'];
} else {
    die(json_encode(Array('s' => 2)));
}
if($_POST['nick']!="")
{
    $form_nick = $_POST['nick'];
} else {
    die(json_encode(Array('s' => 2)));
}
if($_POST['message']!="")
{
    $form_message = $_POST['message'];
} else {
    die(json_encode(Array('s' => 2)));
}

/* Connect to database */
try
{
    $dbc = new PDO("mysql:host=10.0.5.1;dbname=batc_dxspot;charset=utf8", "batc_dxspot", "fRimr2XmQFIsFVR7", array(    
        PDO::ATTR_PERSISTENT => true  
    ));;
}
catch (PDOException $e)
{
    die(json_encode(Array('s' => 3)));
}

$stmt = $dbc->prepare("INSERT INTO legacymessages (room,nick,message) VALUES (?,?,?);");
$stmt->execute(array($form_room,$form_nick,$form_message));
$stmt->closeCursor();
$dbc = null;
print(json_encode(Array('s' => 1)));
?>
