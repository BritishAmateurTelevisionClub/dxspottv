<?php
//session_start();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Repeater Management</title>
<script src="/js/jquery-1.9.1.min.js"></script>
</head>
<body>
<h1>Admin</h1>
<h2>Repeaters</h2>
<ul>
<?php
require_once('spot_login.php');
$repeater_result = mysqli_query($dbc, "SELECT id,callsign FROM repeaters;") or die(mysqli_error($dbc));
while($row = mysqli_fetch_array($repeater_result))
{
?>
<li><a href="edit_repeater.php?id=<?php print $row['id']; ?>"><?php print $row['callsign']; ?></a>
<?php
}
mysql_end($dbc);
?>
</ul>
</body>
</html>
