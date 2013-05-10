<?php
session_start();
//print_r($_COOKIE);
//print "<br>";
if (isset($_COOKIE["auth_error"])) {
  if ($_COOKIE["auth_error"]=="1") {
    // Redirect back after failed login
    $user_known = 0;
    $logged_in = 0;
    $auth_error = 1;
    $auth_error_text = $_COOKIE["auth_error_text"];
  } else {
    include('spot_login.php');
    $callsign_result = mysqli_query($dbc, "SELECT callsign FROM users WHERE id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));
    $callsign_row = mysqli_fetch_array($callsign_result);
    $callsign = $callsign_row["callsign"];
    // Logged in, but check session id is valid
    $sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));  
    if(mysqli_num_rows ($sessions_result)==0) { // session doesn't exist on server
      $user_known = 1;
      $logged_in = 0;
      $auth_error = 1;
      $auth_error_text = "Session not found, please log in.";
    } else {
      $target_row = mysqli_fetch_array($sessions_result);
      if ($_COOKIE["session_key"]==$target_row["session_id"]) {
        // Session matches, so is logged in!
        $user_known = 1;
        $logged_in = 1;
        $auth_error = 0;
      } else {
        // Session doesn't match, make them log in again
        $user_known = 1;
        $logged_in = 0;
        $auth_error = 1;
        $auth_error_text = "Session not found, please log in.";
      }
      //print_r($target_row);
      //print $_COOKIE["session_key"];
      //print "<br>";
      //print $target_row["session_id"];
      //print "<br>";
    }
    mysql_end($dbc);
    $auth_error=0;
  }
} else {
  // Guest User
  $user_known = 0;
  $logged_in = 0;
  $auth_error = 0;
}
//print "User Known: " . $user_known;
//print "Logged In: " . $logged_in;
//print "Auth Error: " . $auth_error;
?>
<html>
<head>
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
<title>DXSpot.TV</title>
<link href="atvspot.css" rel="stylesheet">
<link href="map-default.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<?php include_once("g_analytics.php") ?>
<script type="text/javascript" src="js/map.js"></script>
<script type="text/javascript">
<?php if($user_known) { ?> // Do we fill in callsign as nick for irc
var irc_frame_source = "http://webchat.freenode.net/?channels=#atvspot&nick=<?php print $callsign; ?>&prompt=1"
<?php } else { ?>
var irc_frame_source = "http://webchat.freenode.net/?channels=#atvspot"
<?php } ?> // End of callsign as nick for irc
</script>
<script type="text/javascript" src="js/atvspot-util.js"></script>
<script type="text/javascript" src="js/atvspot.js"></script>
<script type="text/javascript" src="js/atvspot-ui.js"></script>
</head>
<body>
<div style="text-align: center; align: top; height: 100%; ">
<h2>ATV DXSpot</h2>
<table
style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;"
border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td width="60%" style="padding: 5px; vertical-align: top;">

<form action="#">
<table border="0" cellspacing="3" cellpadding="0" width="100%" >
<tr>

<td><p align="center">
<select id="time_select">
  <option value="all">All Spots</option>
  <option value="year">Last year</option>
  <option value="6months">Last 6 months</option>
  <option value="1month">Last Month</option>
  <option value="1week">Last Week</option>
  <option value="24hours">Last 24 Hours</option>
  <option value="12hours">Last 12 Hours</option>
</select>
</td>

<td><p align="center">
<strong>Band:</strong>&nbsp;
<select id="band_select">
  <option value="all">All Bands</option>
  <option value="70cm">70cm</option>
  <option value="23cm">23cm</option>
  <option value="13cm">13cm & above</option>
</select>
<td><p align="center">
<strong>Users:</strong>&nbsp;<input type="checkbox" id="userbox" onClick="boxclick(this,'user')" /></p></td>

<td><p align="center">
<strong>Repeaters:</strong>&nbsp;<input type="checkbox" id="repeaterbox" onClick="boxclick(this,'repeater')" /></p></td>

</tr></table>
</form>


<div id="map_canvas"></div>
</td>
<td width="40%" style="padding: 5px; vertical-align: top;">
<?php
if($logged_in) {
  print "Hi " . $callsign . "!";
?>
&nbsp;&nbsp;<a href="logout.php">Logout</a>
<br><br>
<?
} else {
?>
<form id=login action="login.php" method="post">
<b>Login: </b>
Callsign: <input type=text name="callsign" <?php if($user_known) { print 'value="' . $callsign . '"'; } ?>/>
Password: <input type=password name="passwd" />
<input type=submit text='Login' />
<?php
if ($auth_error==1) {
  print '<font color="red">' . $auth_error_text . '</font>';
} else if ($logged_in==0) {
  print '<a href="register.php">Register New User</a>';
}
?>
</form>
<?php
} // End of greeting/login form
?>
<iframe id='irc_frame' frameborder="0"></iframe><br>
<span id='irc_shown_blurb'>To open the channel in your native IRC client, <a href="irc://chat.freenode.net:6667/#atvspot">click here</a> and <a href="javascript:void(0)" onclick="hideIRC();">hide webIRC</a>.</span>
<span id='irc_hidden_blurb' style="display: none">IRC Chat hidden. To show, <a href="javascript:void(0)" onclick="showIRC();">click here</a>.</span>
<?php
if($logged_in) { // If logged in, show spot form
?>
<h4>I'm currently listening on:</h4>
<form id=listening>
70cm: <input type="checkbox" id="listen_70cm_box" /><span id='listen_70cm_options' style="display: none">&nbsp;<input type="text" id="listen_70cm_freq" value="" />MHz</span><br>
23cm: <input type="checkbox" id="listen_23cm_box" /><span id='listen_23cm_options' style="display: none">&nbsp;<input type="text" id="listen_23cm_freq" value="" />MHz</span><br>
13cm: <input type="checkbox" id="listen_13cm_box" /><span id='listen_13cm_options' style="display: none">&nbsp;<input type="text" id="listen_13cm_freq" value="" />MHz</span>
</form>
<br>
<b>Spot</b>
<form id='spot_form'>
Frequency: <input type=text name="spot_freq"></input>
&nbsp;Mode: <select id="spot_mode_select">
<option value="1">PAL</option>
<option value="2">Digital QPSK</option>
</select>
<br>
<u>Remote Station</u><br>
Callsign: <input type=text name="remote_callsign" id="remote_callsign"></input>
&nbsp;Locator: <input type=text name="remote_loc" id="remote_loc"></input>
<br>
Comments: <input type=text name="spot_comments" length=60></input>
<input type=submit />
</form>
<?php
} else {
?>
<h4>You must be logged in to submit spots.</h4>
<?php
}
?>
</td>
</tr>
</tbody>
</table>
<br>
Copyright 2013 Phil Crump <a href='https://www.thecraag.com/' target='_blank'>thecraag.com</a>
</div>
</body>
</html>
