<?php
header('Content-Type: application/json');
require_once('pdo_login.php');

$output = array();

$stmt = $dbc->prepare("SELECT * FROM all_repeaters;");
$stmt->execute();

if($stmt->rowCount()>0)
{
    $output = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else
{
    $output = Array("error" => "No repeaters found.");
}
$stmt->closeCursor();
$dbc = null;

print json_encode($output);
?>

