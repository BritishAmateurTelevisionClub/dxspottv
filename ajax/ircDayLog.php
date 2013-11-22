<?php
require_once('dxspottv_pdo.php');

$log_stmt = $dbc->prepare("SELECT time,nick,message FROM irclog WHERE time>?;");
$log_stmt->bindValue(1, (time()-86400)*1000, PDO::PARAM_INT);
$log_stmt->execute();
$log_stmt->bindColumn(1, $mtime);
$log_stmt->bindColumn(2, $nick);
$log_stmt->bindColumn(3, $message);
$output = array();
while ($log_stmt->fetch()) {
    $row['time']=$mtime;
    $row['nick']=$nick;
    $row['message']=$message;
    $output[]=$row;
}
print json_encode($output);
?>
