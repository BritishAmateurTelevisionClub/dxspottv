<?php

/* Sanity check user input */
if($_GET['room']!="")
{
    $req_room = $_GET['room'];
} else {
    die(json_encode(Array('s' => 2)));
}
if($_GET['lastid']!="")
{
    $req_id = $_GET['lastid'];
} else {
    die(json_encode(Array('s' => 2)));
}

include('../dxspottv_pdo.php');

$ret_array = Array('s' => 4);
$stmt = $dbc->prepare("SELECT * FROM legacymessages WHERE room=? AND id>? AND ts>=(now() - INTERVAL 1 DAY) ORDER BY ts ASC;");
$stmt->execute(array($req_room, $req_id));
if($stmt->rowCount()>0)
{
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    $message_array = Array();
    $lastid = 0;
    foreach($rows as $row)
    {
        $message_array[] = Array('t' => $row['ts'], 'n' => $row['nick'], 'm' => $row['message']);
        $lastid = $row['id'];
    }
    $rows = null;
    $ret_array = Array('s' => 1, 'ms' => $message_array, 'l' => intval($lastid));
}
else
{
    $stmt->closeCursor();
    $ret_array = Array('s' => 0);
}
$dbc = null;
print(json_encode($ret_array));
?>
