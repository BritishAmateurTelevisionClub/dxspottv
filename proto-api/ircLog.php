<?php
require_once('dxspottv_pdo.php');

$log_stmt = $dbc->prepare("SELECT id,time,nick,message FROM irclog WHERE time > ?;");
$log_stmt->bindValue(1, time()-86400000);
$log_stmt->execute();
$log_stmt->bindColumn(1, $row[0]);
$log_stmt->bindColumn(2, $row[1]);
$log_stmt->bindColumn(3, $row[2]);
$log_stmt->bindColumn(4, $row[3]);

$output = array();
while ($log_stmt->fetch()) {
    array_push($output, $row);
}
print json_encode($output);
?>
