<?php

/* Sanity check user input */
#if($_POST['room']!="")
#{
#    $form_room = $_POST['room'];
#} else {
#    die(json_encode(Array('s' => 2)));
#}
$form_room = 1;
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
include('../dxspottv_pdo.php');

$stmt = $dbc->prepare("INSERT INTO legacymessages (room,nick,message) VALUES (?,?,?);");
$stmt->execute(array($form_room,$form_nick,$form_message));
$stmt->closeCursor();
$dbc = null;
print(json_encode(Array('s' => 1)));
?>
