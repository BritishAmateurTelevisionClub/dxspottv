<?php
session_start();
if(!(isset($_REQUEST["repeater_id"]))) {
	print "Error: No ID requested.";
} else {

require_once('dxspottv_pdo.php');

$repeater_stmt = $dbc->prepare("SELECT callsign, lat, lon, qth_r, qth, 2m, 70cm, 23cm, 13cm, 9cm, 6cm, 3cm, tx1, tx2, tx3, tx4, tx5, tx6, tx7, tx8, tx9, rx1, rx2, rx3, rx4, rx5, rx6, rx7, rx8, rx9, description, website, keeper_callsign, active, height, id FROM all_repeaters WHERE id =?;");
$repeater_stmt->bindValue(1, $_REQUEST["repeater_id"], PDO::PARAM_INT);
$repeater_stmt->execute();
$repeater_stmt->bindColumn(1, $repeater['callsign']);
$repeater_stmt->bindColumn(2, $repeater['latitude']);
$repeater_stmt->bindColumn(3, $repeater['longitude']);
$repeater_stmt->bindColumn(4, $repeater['qth_r']);
$repeater_stmt->bindColumn(5, $repeater['qth']);
$repeater_stmt->bindColumn(6, $repeater['is_2m']);
$repeater_stmt->bindColumn(7, $repeater['is_70cm']);
$repeater_stmt->bindColumn(8, $repeater['is_23cm']);
$repeater_stmt->bindColumn(9, $repeater['is_13cm']);
$repeater_stmt->bindColumn(10, $repeater['is_9cm']);
$repeater_stmt->bindColumn(11, $repeater['is_6cm']);
$repeater_stmt->bindColumn(12, $repeater['is_3cm']);
$repeater_stmt->bindColumn(13, $repeater['tx1']);
$repeater_stmt->bindColumn(14, $repeater['tx2']);
$repeater_stmt->bindColumn(15, $repeater['tx3']);
$repeater_stmt->bindColumn(16, $repeater['tx4']);
$repeater_stmt->bindColumn(17, $repeater['tx5']);
$repeater_stmt->bindColumn(18, $repeater['tx6']);
$repeater_stmt->bindColumn(19, $repeater['tx7']);
$repeater_stmt->bindColumn(20, $repeater['tx8']);
$repeater_stmt->bindColumn(21, $repeater['tx9']);
$repeater_stmt->bindColumn(22, $repeater['rx1']);
$repeater_stmt->bindColumn(23, $repeater['rx2']);
$repeater_stmt->bindColumn(24, $repeater['rx3']);
$repeater_stmt->bindColumn(25, $repeater['rx4']);
$repeater_stmt->bindColumn(26, $repeater['rx5']);
$repeater_stmt->bindColumn(27, $repeater['rx6']);
$repeater_stmt->bindColumn(28, $repeater['rx7']);
$repeater_stmt->bindColumn(29, $repeater['rx8']);
$repeater_stmt->bindColumn(30, $repeater['rx9']);
$repeater_stmt->bindColumn(31, $repeater['description']);
$repeater_stmt->bindColumn(32, $repeater['website']);
$repeater_stmt->bindColumn(33, $repeater['keeper']);
$repeater_stmt->bindColumn(34, $repeater['active']);
$repeater_stmt->bindColumn(35, $repeater['height']);
$repeater_stmt->bindColumn(36, $repeater['id']);
$repeater_stmt->fetch();

print json_encode($repeater);
}
?>
