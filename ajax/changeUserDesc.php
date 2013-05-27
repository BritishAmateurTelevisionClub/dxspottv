<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variable = isset($_REQUEST["description"]);
if($got_cookies && $got_variable) {
	include('spot_login.php');
	$desc = htmlentities(mysqli_real_escape_string($dbc, $_REQUEST["description"]));
	$sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));  
	if(mysqli_num_rows ($sessions_result)==0) { // session doesn't exist on server
		print 'Session not found.';
	} else {
		$target_row = mysqli_fetch_array($sessions_result);
		if ($_COOKIE["session_key"]==$target_row["session_id"]) {
			// Session matches, so is logged in!
			$update_query="UPDATE users set station_desc='{$desc}' WHERE id = '{$_COOKIE["user_id"]}';";
		    mysqli_query($dbc, $update_query) or die(mysqli_error($dbc));
		} else {
			print 'Session doesnt match.';
		}
	}
	mysql_end($dbc);
} else { // Not got cookies
	print 'Access Denied.';
}
?>
