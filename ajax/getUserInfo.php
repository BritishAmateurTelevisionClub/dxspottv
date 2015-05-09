<?php
session_start();
if(isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"])) {
	require_once('../dxspottv_pdo.php');
	$output = array();
	$user_statement = $dbc->prepare("SELECT callsign,lat,lon,locator,station_desc,website,radio_active FROM users WHERE id=?;");
	$user_statement->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
	$user_statement->execute();
	$user_statement->bindColumn(1, $output['callsign']);
	$user_statement->bindColumn(2, $output['lat']);
	$user_statement->bindColumn(3, $output['lon']);
	$user_statement->bindColumn(4, $output['locator']);
	$user_statement->bindColumn(5, $output['description']);
	$user_statement->bindColumn(6, $output['website']);
	$user_statement->bindColumn(7, $output['radio_active']);
	$user_statement->fetch();
} else { // Not got cookies
	$output['error'] = 'No cookies';
}
print json_encode($output);
?>
