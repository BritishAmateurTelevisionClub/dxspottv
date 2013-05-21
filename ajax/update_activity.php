<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
if($got_cookies) {
	include_once("login_functions.php");
	include('spot_login.php');

	$sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));  
	if(mysqli_num_rows ($sessions_result)==0) { // session doesn't exist on server
		print 'Session not found.';
	} else {
		$target_row = mysqli_fetch_array($sessions_result);
		if ($_COOKIE["session_key"]==$target_row["session_id"]) {
			// Session matches, so is logged in!
			$user_id = $_COOKIE["user_id"];
			$sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $user_id . "';") or die(mysqli_error($dbc));
			if(mysqli_num_rows ($sessions_result)==0) {
				$insert_query="INSERT into sessions (session_id, user_id) VALUES ('{$session_key}', '{$user_id}');";
				$ret = mysqli_query($dbc, $insert_query) or die(mysqli_error($dbc));
			} else {
				$update_query="UPDATE sessions set session_id='{$session_key}',activity=NOW() where user_id = '{$user_id}';";
				$ret = mysqli_query($dbc, $update_query) or die(mysqli_error($dbc));
			}
		} else {
			print 'Session doesnt match.';
		}
	}
} else { // Not got cookies
	print 'Access Denied.';
}
?>
