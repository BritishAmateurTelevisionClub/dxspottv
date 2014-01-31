<?php
require_once('dxspottv_pdo.php');

$output = array();

$output['users']=array();
$userRow = array()
$user_stmt = $dbc->prepare("SELECT users.id,users.known,users.radio_active,users.callsign,users.lat,users.lon,users.locator,TIME_TO_SEC(TIMEDIFF(NOW(),sessions.activity)) FROM users LEFT JOIN sessions on sessions.user_id=users.id;");
$user_stmt->execute();
$user_stmt->bindColumn(1, $userRow['i']);
$user_stmt->bindColumn(2, $userRow['k']);
$user_stmt->bindColumn(3, $userRow['ra']);
$user_stmt->bindColumn(4, $userRow['c']);
$user_stmt->bindColumn(5, $userRow['lat']);
$user_stmt->bindColumn(6, $userRow['lon']);
$user_stmt->bindColumn(7, $userRow['loc']);
$user_stmt->bindColumn(7, $userRow['act']);

while ($user_stmt->fetch()) {
    if($userRow['act']==NULL) {
        $userRow['act']=9999;
    }
    array_push($output['users'], $userRow);
}

$output['spots']=array();
$spotRow = array()
$spots_stmt = $dbc->prepare("SELECT id,band_id,mode_id,primary_id,secondary_id,secondary_isrepeater,spot_time,comments,TIME_TO_SEC(TIMEDIFF(NOW(),spot_time)) AS seconds_ago FROM spots WHERE spot_time>DATE_SUB(now(), INTERVAL 1 DAY) ORDER BY spot_time DESC;");
$spots_stmt->execute();
$spots_stmt->bindColumn(1, $spotRow['i']);
$spots_stmt->bindColumn(2, $spotRow['b']);
$spots_stmt->bindColumn(3, $spotRow['m']);
$spots_stmt->bindColumn(4, $spotRow['p']);
$spots_stmt->bindColumn(5, $spotRow['s']);
$spots_stmt->bindColumn(6, $spotRow['sr']);
$spots_stmt->bindColumn(7, $spotRow['t']);
$spots_stmt->bindColumn(8, $spotRow['c']);
$spots_stmt->bindColumn(9, $spotRow['rt']);

while ($spots_stmt->fetch()) {
    array_push($output['spots'], $spotRow);
}

print json_encode($output);
?>
