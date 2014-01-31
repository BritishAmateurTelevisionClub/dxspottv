<?php
require_once('dxspottv_pdo.php');

$output = array();

$output['users']=array();
$userRow = array();
$user_stmt = $dbc->prepare("SELECT users.id AS i,users.known AS k,users.radio_active AS ra,users.callsign AS c,users.lat AS la,users.lon AS lo,users.locator AS loc,TIME_TO_SEC(TIMEDIFF(NOW(),sessions.activity)) AS activity FROM users LEFT JOIN sessions on sessions.user_id=users.id;");
$user_stmt->execute();
$user_stmt->bindColumn(1, $userRow['i']);
$user_stmt->bindColumn(2, $userRow['k']);
$user_stmt->bindColumn(3, $userRow['ra']);
$user_stmt->bindColumn(4, $userRow['c']);
$user_stmt->bindColumn(5, $userRow['lat']);
$user_stmt->bindColumn(6, $userRow['lon']);
$user_stmt->bindColumn(7, $userRow['loc']);
$user_stmt->bindColumn(7, $userRow['act']);

while ($userRow = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
    if($userRow['act']==NULL) {
        $userRow['act']=9999;
    }
    $output['users'][] = $userRow;
}


$output['spots']=array();
$spotRow = array();
$spots_stmt = $dbc->prepare("SELECT id AS i,band_id AS b,mode_id AS m,primary_id AS p,secondary_id AS s,secondary_isrepeater AS sr,spot_time AS t,comments AS c,TIME_TO_SEC(TIMEDIFF(NOW(),spot_time)) AS rt FROM spots WHERE spot_time>DATE_SUB(now(), INTERVAL 1 DAY) ORDER BY spot_time DESC;");
$spots_stmt->execute();

while ($spotRow = $spot_stmt->fetch(PDO::FETCH_ASSOC)) {
    $output['spots'][] = $spotRow;
}

print json_encode($output);
?>
