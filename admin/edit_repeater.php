<?php
//session_start();

?>
<html>
<head>
<title><?php print $callsign; ?> - Edit</title>
</head>
<body>
<h1>Admin</h1>
<h2>Edit Repeater</h2>
<ul>
<?php
include('../spot_login.php');
<?
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
