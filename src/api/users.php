<?php
header('Content-Type: application/json');
require_once('pdo_login.php');

$output = array();

$stmt = $dbc->prepare("SELECT users.id, users.known, users.radio_active, users.name, users.callsign, users.lat, users.lon, users.locator, users.station_desc, users.website, TIME_TO_SEC(TIMEDIFF(NOW(),MAX(sessions.activity))) AS activity_timer FROM users LEFT JOIN sessions on sessions.user_id=users.id GROUP BY users.id");
$stmt->execute();

if($stmt->rowCount()>0)
{
    $output = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else
{
    $output = Array("error" => "No users found.");
}
$stmt->closeCursor();
$dbc = null;

print json_encode($output);
?>

