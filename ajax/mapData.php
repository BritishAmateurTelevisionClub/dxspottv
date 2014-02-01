<?php
header('Content-Type: application/json');
require_once('dxspottv_pdo.php');

$repeater_input_freqs = array();
$repeater_input_freqs[9] = "rx1";
$repeater_input_freqs[10] = "rx2";
$repeater_input_freqs[11] = "rx3";
$repeater_input_freqs[12] = "rx4";
$repeater_input_freqs[13] = "rx5";
$repeater_input_freqs[14] = "rx6";
$repeater_input_freqs[15] = "rx7";
$repeater_input_freqs[16] = "rx8";
$repeater_input_freqs[17] = "rx9";

$repeater_output_freqs = array();
$repeater_output_freqs[0] = "tx1";
$repeater_output_freqs[1] = "tx2";
$repeater_output_freqs[2] = "tx3";
$repeater_output_freqs[3] = "tx4";
$repeater_output_freqs[4] = "tx5";
$repeater_output_freqs[5] = "tx6";
$repeater_output_freqs[6] = "tx7";
$repeater_output_freqs[7] = "tx8";
$repeater_output_freqs[8] = "tx9";

$repeater_bands = array();
$repeater_bands[0] = "2m";
$repeater_bands[1] = "70cm";
$repeater_bands[2] = "23cm";
$repeater_bands[3] = "13cm";
$repeater_bands[4] = "9cm";
$repeater_bands[5] = "6cm";
$repeater_bands[6] = "3cm";

$output = array();

$output['repeaters']=array();

$repeater_stmt = $dbc->prepare("SELECT id,lat,lon,height,callsign,qth_r,qth,ngr,tx1,tx2,tx3,tx4,tx5,tx6,tx7,tx8,tx9,rx1,rx2,rx3,rx4,rx5,rx6,rx7,rx8,rx9,70cm,23cm,13cm,9cm,6cm,3cm,description,website,keeper_callsign,active,updated FROM all_repeaters;");
$repeater_stmt->execute();

while ($reptrRow = $repeater_stmt->fetch(PDO::FETCH_ASSOC)) {
    $opRow=array();
    $opRow['id']=$reptrRow['id'];
    $opRow['qrz']=$reptrRow['callsign'];
    $opRow['lat']=$reptrRow['lat'];
    $opRow['lon']=$reptrRow['lon'];
    $opRow['haat']=$reptrRow['height'];
    $opRow['loc']=$reptrRow['qth_r'];
    if($reptrRow['qth']!=NULL) {
        $opRow['qth']=$reptrRow['qth'];
    }
    if($reptrRow['ngr']!=NULL) {
        $opRow['ngr']=$reptrRow['ngr'];
    }
    if($reptrRow['website']!=NULL) {
        $opRow['www']=$reptrRow['website'];
    }
    if($reptrRow['keeper_callsign']!="") {
        $opRow['keep']=$reptrRow['keeper_callsign'];
    }
    if($reptrRow['active']==1) {
        $opRow['op']=1;
    }
    
    foreach ($repeater_input_freqs as $input_freq) {
        if($reptrRow[$input_freq]!=NULL) {
            $opRow[$input_freq] = $reptrRow[$input_freq];
        }
    }
    
    foreach ($repeater_output_freqs as $output_freq) {
        if($reptrRow[$output_freq]!=NULL) {
            $opRow[$output_freq] = $reptrRow[$output_freq];
        }
    }
    
    foreach ($repeater_bands as $band) {
        if($reptrRow[$band]!=NULL) {
            $opRow[$band] = $reptrRow[$band];
        }
    }
    
    $output['repeaters'][] = $opRow;
}

$output['users']=array();
$userRow = array();
$user_stmt = $dbc->prepare("SELECT users.id AS i,users.known AS k,users.radio_active AS ra,users.callsign AS c,users.lat AS la,users.lon AS lo,users.locator AS loc,TIME_TO_SEC(TIMEDIFF(NOW(),sessions.activity)) AS act FROM users LEFT JOIN sessions on sessions.user_id=users.id;");
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
