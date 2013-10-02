<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Repeater Management</title>
</head>
<body>
<h1>Admin</h1>
<a href="/admin/index.html"><h3>Back to Index</h2></a>
<h2>Repeaters</h2>
<ul>
<?php
require_once('dxspottv_login.php');
$repeater_result = mysqli_query($dbc, "SELECT id,callsign FROM repeaters;") or die(mysqli_error($dbc));
while($row = mysqli_fetch_array($repeater_result))
{
?>
<li><a href="editRepeater.html?id=<?php print $row['id']; ?>"><?php print $row['callsign']; ?></a>
<?php
}
mysql_end($dbc);
?>
</ul>
</body>
</html>
