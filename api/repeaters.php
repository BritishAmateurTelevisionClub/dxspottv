<?php
header('Content-Type: application/json');
require_once('../dxspottv_pdo.php');

$output = array();

$stmt = $dbc->prepare("SELECT * FROM all_repeaters WHERE active=1;");
//$stmt = $dbc->prepare("SELECT id AS i,band_id AS b,mode_id AS m,primary_id AS p,secondary_id AS s,secondary_isrepeater AS sr,spot_time AS t,comments AS c,TIME_TO_SEC(TIMEDIFF(NOW(),spot_time)) AS rt FROM spots WHERE spot_time>DATE_SUB(now(), INTERVAL 1 DAY) ORDER BY spot_time DESC;");
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
