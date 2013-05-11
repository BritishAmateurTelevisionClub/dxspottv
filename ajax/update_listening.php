<?php
session_start();
$got_cookies = (isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"]));
$got_variables = (isset($_REQUEST["l70cm"]) && isset($_REQUEST["l23cm"]));
if($got_cookies && $got_variables) {
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
		$l70cm = mysqli_real_escape_string($dbc, $_REQUEST["l70cm"]);
		$l70cm_freq = mysqli_real_escape_string($dbc, $_REQUEST["l70cm_freq"]);
		$l23cm = mysqli_real_escape_string($dbc, $_REQUEST["l23cm"]);
		$l23cm_freq = mysqli_real_escape_string($dbc, $_REQUEST["l23cm_freq"]);
		$l13cm = mysqli_real_escape_string($dbc, $_REQUEST["l13cm"]);
		$l13cm_freq = mysqli_real_escape_string($dbc, $_REQUEST["l13cm_freq"]);
		$check_existing_result = mysqli_query($dbc, "SELECT user_id FROM listening WHERE user_id='{$user_id}';") or die(mysqli_error($dbc));
		if(mysqli_num_rows ($check_existing_result)==0) {
			$listen_query = "INSERT into listening (user_id, 70cm_listen, 70cm_freq, 23cm_listen, 23cm_freq, 13cm_listen, 13cm_freq) VALUES ('{$user_id}', '{$l70cm}', '{$l70cm_freq}', '{$l23cm}', '{$l23cm_freq}', '{$l13cm}', '{$l13cm_freq}');";
		} else {
			$listen_query = "UPDATE listening SET 70cm_listen='{$l70cm}', 70cm_freq='{$l70cm_freq}', 23cm_listen='{$l23cm}', 23cm_freq='{$l23cm_freq}', 13cm_listen='{$l13cm}', 13cm_freq='{$l13cm_freq}') WHERE user_id='{$user_id}';";
        }
        mysqli_query($dbc, $listen_query) or die(mysqli_error($dbc));
	} else {
        print 'Session doesnt match.';
	}
}
} else { // Not got cookies or variables
print 'Access Denied.';
}
?>
