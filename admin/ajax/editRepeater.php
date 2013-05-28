<?php

$got_variables = (isset($_REQUEST["repeater"]) && isset($_REQUEST["callsign"]) && isset($_REQUEST["locator"]) && isset($_REQUEST["location"]) && isset($_REQUEST["description"]) && isset($_REQUEST["website"]) && isset($_REQUEST["keeper"]) && isset($_REQUEST["active"]));

$output = array();
if($got_variables) {
	require_once('spot_login.php');

	$update_statement = $dbc->prepare("UPDATE repeaters set callsign=?,qth_r=?,qth=?,description=?,website=?,keeper_callsign=?,active=? WHERE id=?;");
	$update_statement->bind_param('ssssssi',
		htmlentities($_REQUEST["callsign"]),
		htmlentities($_REQUEST["locator"]),
		htmlentities($_REQUEST["location"]),
		htmlentities($_REQUEST["description"]),
		htmlentities($_REQUEST["website"]),
		htmlentities($_REQUEST["keeper"]),
		htmlentities($_REQUEST["active"]),
		htmlentities($_REQUEST["repeater"]));
	$update_statement->execute();
	$update_statement->close();
	
	if($update_statement->affected_rows==1) {
		$output['success'] = 1;
	} else {
		$output['error'] = 2; // MYSQL Error
	}
} else {
	$output['error'] = 1;
}
print json_encode($output);
?>
