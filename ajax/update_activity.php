<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
if($got_cookies) {
	require_once('spot_login.php');

	$sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));  
	if(mysqli_num_rows ($sessions_result)==0) { // session doesn't exist on server
		print 'Session not found.';
	} else {
		while($target_row = mysqli_fetch_array($sessions_result)) { // find a matching session
			if ($_COOKIE["session_key"]==$target_row["session_id"]) {
				// Session matches, so is logged in!
				$update_query="UPDATE sessions set activity=NOW() where session_id = '{$_COOKIE["session_id"]}';";
				mysqli_query($dbc, $update_query) or die(mysqli_error($dbc));
			}
		}
	}
	mysql_end($dbc);
} else { // Not got cookies
	print 'Access Denied.';
}
?>
