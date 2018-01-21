<?php
session_start();

/* Sanity check user input */
if($_POST['room']!="")
{
    $req_room = $_POST['room'];
} else {
    die(json_encode(Array('s' => 2)));
}
if($_POST['nick']!="")
{
    $req_nick = urldecode($_POST['nick']);
    if(!isset($_SESSION["nick"])) {
	$_SESSION["nick"] = $req_nick;
    }
}
else if(isset($_SESSION["nick"]))
{
    $req_nick = $_SESSION["nick"];
    $recovered_nick = 1;
}

/* Connect to database */
include('../dxspottv_pdo.php');
$ret_array = Array('s' => 4);

if(isset($req_nick)) {
/* Update Our Status first */
$stmt = $dbc->prepare("SELECT * FROM currentusers WHERE room=? AND nick=?;");
$stmt->execute(array($req_room,$req_nick));
if($stmt->rowCount()>0)
{
    $stmt->closeCursor();
    $stmt = $dbc->prepare("UPDATE currentusers SET lastts=CURRENT_TIMESTAMP(), ip=? WHERE room=? AND nick=?;");
    $stmt->execute(array($_SERVER["REMOTE_ADDR"],$req_room,$req_nick));
    $stmt->closeCursor();
} else {
    $stmt->closeCursor();
    $stmt = $dbc->prepare("INSERT INTO currentusers (room,nick,ip) VALUES (?,?,?);");
    $stmt->execute(array($req_room,$req_nick,$_SERVER["REMOTE_ADDR"]));
    $stmt->closeCursor();
}
}

$stmt = $dbc->prepare("SELECT nick FROM currentusers WHERE room=? AND lastts>=(now() - INTERVAL 10 SECOND) ORDER BY nick ASC;");
$stmt->execute(array($req_room));
if($stmt->rowCount()>0)
{
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    $nick_array = Array();
    foreach($rows as $row)
    {
        $nick_array[] = $row['nick'];
    }
    $rows = null;

    if(isset($recovered_nick))
    {
        $ret_array = Array('s' => 10, 'rn' => $req_nick, 'us' => $nick_array);
    }
    else
    {
        $ret_array = Array('s' => 1, 'us' => $nick_array);
    }
}
else
{
    $stmt->closeCursor();
    $ret_array = Array('s' => 0);
}
$dbc = null;
print(json_encode($ret_array));
?>
