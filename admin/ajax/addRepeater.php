<?php

if(isset($_REQUEST["callsign"]) && isset($_REQUEST["locator"]) && isset($_REQUEST["lat"]) && isset($_REQUEST["lon"])) {
    require_once("dxspottv_pdo.php");
	
	$insert_statement = $dbc->prepare("INSERT into all_repeaters (callsign, lat, lon, qth_r, qth, 2m, 70cm, 23cm, 13cm, 9cm, 6cm, 3cm, tx1, tx2, tx3, tx4, tx5, tx6, tx7, tx8, tx9, rx1, rx2, rx3, rx4, rx5, rx6, rx7, rx8, rx9, description, website, keeper_callsign, active, height) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
	
	$insert_statement->bindValue(1, htmlentities(strtoupper($_REQUEST["callsign"])), PDO::PARAM_STR);
	$insert_statement->bindValue(2, $_REQUEST["lat"]);
	$insert_statement->bindValue(3, $_REQUEST["lon"]);
	$insert_statement->bindValue(4, htmlentities(strtoupper($_REQUEST["locator"])), PDO::PARAM_STR); // qth_r
	$insert_statement->bindValue(5, htmlentities($_REQUEST["location"]), PDO::PARAM_STR); // qth
	
	$insert_statement->bindValue(6, $_REQUEST["is_2m"], PDO::PARAM_INT); // 2m
	$insert_statement->bindValue(7, $_REQUEST["is_70cm"], PDO::PARAM_INT); // 70cm
	$insert_statement->bindValue(8, $_REQUEST["is_23cm"], PDO::PARAM_INT); // 23cm
	$insert_statement->bindValue(9, $_REQUEST["is_13cm"], PDO::PARAM_INT); // 13cm
	$insert_statement->bindValue(10, $_REQUEST["is_9cm"], PDO::PARAM_INT); // 9cm
	$insert_statement->bindValue(11, $_REQUEST["is_6cm"], PDO::PARAM_INT); // 6cm
	$insert_statement->bindValue(12, $_REQUEST["is_3cm"], PDO::PARAM_INT); // 3cm
	
	$insert_statement->bindValue(13, $_REQUEST["tx1"]);
	$insert_statement->bindValue(14, $_REQUEST["tx2"]);
	$insert_statement->bindValue(15, $_REQUEST["tx3"]);
	$insert_statement->bindValue(16, $_REQUEST["tx4"]);
	$insert_statement->bindValue(17, $_REQUEST["tx5"]);
	$insert_statement->bindValue(18, $_REQUEST["tx6"]);
	$insert_statement->bindValue(19, $_REQUEST["tx7"]);
	$insert_statement->bindValue(20, $_REQUEST["tx8"]);
	$insert_statement->bindValue(21, $_REQUEST["tx9"]);
	
	$insert_statement->bindValue(22, $_REQUEST["rx1"]);
	$insert_statement->bindValue(23, $_REQUEST["rx2"]);
	$insert_statement->bindValue(24, $_REQUEST["rx3"]);
	$insert_statement->bindValue(25, $_REQUEST["rx4"]);
	$insert_statement->bindValue(26, $_REQUEST["rx5"]);
	$insert_statement->bindValue(27, $_REQUEST["rx6"]);
	$insert_statement->bindValue(28, $_REQUEST["rx7"]);
	$insert_statement->bindValue(29, $_REQUEST["rx8"]);
	$insert_statement->bindValue(30, $_REQUEST["rx9"]);
	
	$insert_statement->bindValue(31, htmlentities($_REQUEST["description"]), PDO::PARAM_STR);
	$insert_statement->bindValue(32, htmlentities($_REQUEST["website"]), PDO::PARAM_STR);
	$insert_statement->bindValue(33, htmlentities($_REQUEST["keeper"]), PDO::PARAM_STR);
	$insert_statement->bindValue(34, $_REQUEST["active"], PDO::PARAM_INT);
	$insert_statement->bindValue(35, $_REQUEST["height"], PDO::PARAM_INT);
	
	$insert_statement->execute();
	
	if($insert_statement->rowCount()==1) {
		$output['success'] = 1;
	} else {
		$output['error'] = 2; // MySQL Error
		$output['affected'] = $insert_statement->rowCount();
	}
} else {
	$output['error'] = 1; // Form Error
}
print json_encode($output);
?>
