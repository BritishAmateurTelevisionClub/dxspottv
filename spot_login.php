<?php
$dbc = mysqli_connect('localhost', 'spot', 'MGJCmjA4mFSsCjVE', 'spot') or die('Error connecting to mysql!:' . mysqli_error($dbc));

function mysql_die()
{
	mysqli_close($dbc) or print('Error closing mysql connection!');
	die();
}

function mysql_end($dbc)
{
	mysqli_close($dbc) or die('Error closing mysql connection!');
}

function escape($dbc, $str)
{
	return addcslashes(mysqli_real_escape_string($dbc,$str), '%_');
}
?>
