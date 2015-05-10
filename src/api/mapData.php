<?php
header('Content-Type: application/json');
require_once('pdo_login.php');

$output = array();

$stmt = $dbc->prepare("SELECT NOW() as updated;");
$stmt->execute();
if($stmt->rowCount()>0)
{
    $output['updated'] = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['updated'];
}
$stmt->closeCursor();

if(isset($_GET['since']))
{
    $stmt = $dbc->prepare("SELECT users.id, users.known, users.radio_active, users.name, users.callsign, users.lat, users.lon, users.locator, users.station_desc, users.website, TIME_TO_SEC(TIMEDIFF(NOW(),MAX(sessions.activity))) AS activity_timer FROM users LEFT JOIN sessions on sessions.user_id=users.id WHERE (users.updated>? OR MAX(sessions.activity)>?) GROUP BY users.id");
    $stmt->execute($_GET['since'],$_GET['since']);

    if($stmt->rowCount()>0)
    {
        $output['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $output['users'] = Array();
    }
    $stmt->closeCursor();

    $stmt = $dbc->prepare("SELECT * FROM all_repeaters WHERE updated>?;");
    $stmt->execute($_GET['since']);
    if($stmt->rowCount()>0)
    {
        $output['repeaters'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $output['repeaters'] = Array();
    }
    $stmt->closeCursor();
    
    $stmt = $dbc->prepare("SELECT * FROM spots WHERE spot_time>?;");
    $stmt->execute($_GET['since']);
    if($stmt->rowCount()>0)
    {
        $output['spots'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $output['spots'] = Array();
    }
    $stmt->closeCursor();
}
else
{    
    $stmt = $dbc->prepare("SELECT users.id, users.known, users.radio_active, users.name, users.callsign, users.lat, users.lon, users.locator, users.station_desc, users.website, TIME_TO_SEC(TIMEDIFF(NOW(),MAX(sessions.activity))) AS activity_timer FROM users LEFT JOIN sessions on sessions.user_id=users.id GROUP BY users.id");
    $stmt->execute();

    if($stmt->rowCount()>0)
    {
        $output['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $output['users'] = Array("error" => "No users found.");
    }
    $stmt->closeCursor();

    $stmt = $dbc->prepare("SELECT * FROM all_repeaters;");
    $stmt->execute();
    if($stmt->rowCount()>0)
    {
        $output['repeaters'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $output['repeaters'] = Array("error" => "No repeaters found.");
    }
    $stmt->closeCursor();
    
    $stmt = $dbc->prepare("SELECT * FROM spots;");
    $stmt->execute();
    if($stmt->rowCount()>0)
    {
        $output['spots'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $output['spots'] = Array();
    }
    $stmt->closeCursor();
}
$dbc = null;

print json_encode($output);
?>
