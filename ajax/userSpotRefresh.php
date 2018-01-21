<?php
header('Content-Type: application/json');
require_once('../dxspottv_pdo.php');

$output = array();

$output['users']=array();
$userRow = array();
$user_stmt = $dbc->prepare("SELECT users.id AS i,users.known AS k,users.radio_active AS ra,users.callsign AS c,users.lat AS la,users.lon AS lo,users.locator AS loc,users.station_desc AS sd,users.website as w,TIME_TO_SEC(TIMEDIFF(NOW(),sessions.activity)) AS act FROM users LEFT JOIN (SELECT user_id, MAX(activity) AS activity FROM sessions GROUP BY user_id) AS sessions on sessions.user_id=users.id;");
$user_stmt->execute();

while ($userRow = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
    if($userRow['act']==NULL) {
        $userRow['act']=9999;
    }
    $output['users'][] = $userRow;
}


$output['spots']=array();
$spotRow = array();
$spot_stmt = $dbc->prepare("SELECT id AS i,band_id AS b,mode_id AS m,primary_id AS p,secondary_id AS s,secondary_isrepeater AS sr,spot_time AS t,comments AS c,TIME_TO_SEC(TIMEDIFF(NOW(),spot_time)) AS rt FROM spots WHERE spot_time>DATE_SUB(now(), INTERVAL 6 MONTH) ORDER BY spot_time DESC;");
$spot_stmt->execute();

while ($spotRow = $spot_stmt->fetch(PDO::FETCH_ASSOC)) {
    $output['spots'][] = $spotRow;
}

print json_encode($output);
?>
