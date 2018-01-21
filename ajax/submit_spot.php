<?php
session_start();
$output = array();
$output['success'] = 0;
$output['error'] = 0;

$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = (isset($_REQUEST["band_id"]) && isset($_REQUEST["mode"]));

if($got_cookies && $got_variables) {
	require_once("../dxspottv_pdo.php");
	$sessions_statement = $dbc->prepare("SELECT session_id FROM sessions WHERE user_id=?;");
        $sessions_statement->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
        $sessions_statement->execute();
        $sessions_statement->bindColumn(1, $sessions_result);
        if($sessions_statement->rowCount()==0) { // session doesn't exist on server
		$output['success'] = 0;
		$output['error'] = 2;
        } else {
                while ($sessions_statement->fetch()) {
                        if ($_COOKIE["session_key"]==$sessions_result) { // Session matches, so is logged in!
				/* Search Users */
                                $user_search_statement = $dbc->prepare("SELECT id FROM users WHERE callsign=? LIMIT 1;");
                                $user_search_statement->bindValue(1, htmlentities(strtoupper($_REQUEST["r_callsign"])));
                                $user_search_statement->execute();
				if($user_search_statement->rowCount()>0) {
                			$user_search_result = $user_search_statement->fetchColumn();
					/* Reject self-spots */
					if($user_search_result == $_COOKIE["user_id"]) {
                                        	$output['success'] = 0;
                                        	$output['error'] = 3;
                                        	die(json_encode($output));
					}
					$spot_statement = $dbc->prepare("INSERT into spots (mode_id, band_id, primary_id, secondary_id, comments) VALUES (?, ?, ?, ?, ?);");
					$spot_statement->bindValue(1, $_REQUEST["mode"], PDO::PARAM_INT);
					$spot_statement->bindValue(2, $_REQUEST["band_id"], PDO::PARAM_INT);
					$spot_statement->bindValue(3, $_COOKIE["user_id"], PDO::PARAM_INT);
					$spot_statement->bindValue(4, $user_search_result, PDO::PARAM_INT);
					$spot_statement->bindValue(5, htmlentities($_REQUEST["comments"]), PDO::PARAM_STR);
					$spot_statement->execute();

                                }
				else
				{
					/* Search Repeaters */
                                	$repeater_search_statement = $dbc->prepare("SELECT id FROM all_repeaters WHERE callsign=? LIMIT 1;");
                                	$repeater_search_statement->bindValue(1, htmlentities(strtoupper($_REQUEST["r_callsign"])), PDO::PARAM_STR);
                                	$repeater_search_statement->execute();
					if($repeater_search_statement->rowCount()>0) {
                				$repeater_search_result = $repeater_search_statement->fetchColumn();
						$spot_statement = $dbc->prepare("INSERT into spots (mode_id, band_id, primary_id, secondary_id, secondary_isrepeater, comments) VALUES (?, ?, ?, ?, '1', ?);");
						$spot_statement->bindValue(1, $_REQUEST["mode"], PDO::PARAM_INT);
						$spot_statement->bindValue(2, $_REQUEST["band_id"], PDO::PARAM_INT);
						$spot_statement->bindValue(3, $_COOKIE["user_id"], PDO::PARAM_INT);
						$spot_statement->bindValue(4, $repeater_search_result, PDO::PARAM_INT);
						$spot_statement->bindValue(5, htmlentities($_REQUEST["comments"]), PDO::PARAM_STR);
						$spot_statement->execute();
					}
                                        else
					{
						/* Unknown user, add to table */
						$newuser_statement = $dbc->prepare("INSERT into users (callsign, locator, lat, lon, known) VALUES (?, ?, ?, ?, '0');");
						$newuser_statement->bindValue(1, htmlentities(strtoupper($_REQUEST["r_callsign"])), PDO::PARAM_STR);
						$newuser_statement->bindValue(2, htmlentities(strtoupper($_REQUEST["r_locator"])), PDO::PARAM_STR);
						$newuser_statement->bindValue(3, $_REQUEST["r_lat"]);
						$newuser_statement->bindValue(4, $_REQUEST["r_lon"]);
						$newuser_statement->execute();

						/* Select id of new user */
						$userid_statement = $dbc->prepare("SELECT id FROM users WHERE callsign = ?;");
						$userid_statement->bindValue(1, htmlentities(strtoupper($_REQUEST["r_callsign"])), PDO::PARAM_STR);
						$userid_statement->execute();
						$userid_statement->bindColumn(1, $newuser_id);
						$userid_statement->fetch();

						/* Insert spot */
						$spot_statement = $dbc->prepare("INSERT into spots (mode_id, band_id, primary_id, secondary_id, comments) VALUES (?, ?, ?, ?, ?);");
                                       		$spot_statement->bindValue(1, $_REQUEST["mode"], PDO::PARAM_INT);
                                       	 	$spot_statement->bindValue(2, $_REQUEST["band_id"], PDO::PARAM_INT);
                                       	 	$spot_statement->bindValue(3, $_COOKIE["user_id"], PDO::PARAM_INT);
                                       	 	$spot_statement->bindValue(4, $newuser_id, PDO::PARAM_INT);
                                       	 	$spot_statement->bindValue(5, htmlentities($_REQUEST["comments"]), PDO::PARAM_STR);
                                       	 	$spot_statement->execute();
					}
				}
				$output['success'] = 1;
                        	$output['error'] = 0;
                        }
                }
        }
} else { // Not got cookies
	$output['success'] = 0;
	$output['error'] = 1;
}
print json_encode($output);
?>
