<?php
session_start();
//print_r($_COOKIE);
//print "<br>";
if (isset($_COOKIE["auth_error"])) {
  if ($_COOKIE["auth_error"]=="1") {
  	// Unset Auth Error
  	setcookie("auth_error", "", time()-3600);
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
<link href="css/atvspot.css" rel="stylesheet">
<link href="css/map-default.css" rel="stylesheet">
<link href="css/flick/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-37815608-6', 'dxspot.tv');
  ga('send', 'pageview');
</script>
<script type="text/javascript" src="js/map.js"></script>
<script type="text/javascript">
<?php if($user_known) { ?> // Do we fill in callsign as nick for irc
var irc_frame_source = "http://webirc.dxspot.tv/?channels=#dxspottv&nick=<?php print $callsign; ?>";
<?php } else { ?>
var irc_frame_source = "http://webirc.dxspot.tv/?channels=#dxspottv";
<?php } ?> // End of callsign as nick for irc
</script>
<script type="text/javascript" src="js/atvspot.js"></script>
<script type="text/javascript" src="js/atvspot-ajax.js"></script>
<script type="text/javascript" src="js/atvspot-ui.js"></script>
<script type="text/javascript" src="js/atvspot-util.js"></script>
<script type="text/javascript" src="/js/locator.js"></script>
</head>
<body>
<div style="text-align: center; align: top; height: 100%; ">
<h2>ATV DXSpot - <font color="red">Alpha</font></h2>
<table
style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;"
border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td width="55%" style="padding: 5px; vertical-align: top;">

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
  <option value="24hours" selected="selected">Last 24 Hours</option>
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
<strong>Users:</strong>&nbsp;<input type="checkbox" id="userBox" /></p></td>

<td><p align="center">
<strong>Repeaters:</strong>&nbsp;<input type="checkbox" id="repeaterBox" /></p></td>

</tr></table>
</form>
<div id="map_canvas"></div>
<?php if($logged_in) { // If logged in, show spot form ?>
<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;"
border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="50%">
<div id="spotLogDiv">
	<h4>Global Spot Log</h4>
	<span id="spotLog" class="reduce-font-size">Loading...</span>
</div>
</td><td style="width: 50%; margin-left: 5px; line-height: 1.3">
<div id="spotForm">
		<b>New Spot</b><br>
		<select id="spot_band_select">
		<option value=1>70cm</option>
		<option value=2>23cm</option>
		<option value=3>13cm</option>
		<option value=4>3cm</option>
		</select>
		&nbsp;
		<select id="spot_mode_select">
		<option value="analogtv">Analog TV</option>
		<option value="digitaltv">Digital TV</option>
		</select><br>
		<b>Remote</b>&nbsp;Callsign:&nbsp;&nbsp;<input type=text name="remote_callsign" id="remote_callsign" class="spot_box_short" />
		<br><span class="spotFormLabel">Locator:</span>&nbsp;&nbsp;<input type=text name="remote_loc" id="remote_loc" class="spot_box_short" /><br>
		Freq / Comments:<br><input type=text name="spot_comments" id="spot_comments" class="spot_box_long" /><br>
		<button class="spot-button reduce-font-size" id="spot_button">Submit Spot</button>&nbsp;<span id="submitStatus"></span>
	</div>
</td></tr></table>
<?php } else { ?>
<div id="spotLogDiv">
	<h4>Global Spot Log</h4>
	<span id="spotLog" class="reduce-font-size">Loading...</span>
</div>
<?php } ?>
</td>
<td width="45%" style="padding: 5px; vertical-align: top;">
<?php
if($logged_in) {
  print "Hi " . $callsign . "!";
?>
&nbsp;&nbsp;<button class="logout-button reduce-font-size" id="logout_button">Logout</button>
<br><br>
<?php
} else {
?>
Callsign: <input type=text name="callsign" id="callsign_input" <?php if($user_known) { print 'value="' . $callsign . '"'; } ?>/>
Password: <input type=password name="passwd" id="passwd_input" />
&nbsp;&nbsp;<button class="login-button reduce-font-size" id="login_button">Log In</button>
&nbsp;&nbsp;<button class="register-button reduce-font-size" id="register_button">Register</button>
<?php
if ($auth_error==1) {
?>
<div class="ui-state-error ui-corner-all reduce-font-size" style="padding: 0em .7em;">
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		<strong>Alert:</strong>&nbsp;<?php print $auth_error_text; ?></p>
</div>
<?php
} // End of auth_error.
} // End of greeting/login form
?>
<div id="tabs">
	<ul>
		<li><a href="#webIRC" class="reduce-font-size">IRC Chat</a></li>
		<?php
		if($logged_in) { // If logged in, show spot form
		?>
		<li><a href="#editStation" class="reduce-font-size">Edit My Station</a></li>
		<?php } ?>
		<li><a href="#helpTab" class="reduce-font-size">Help</a></li>
		<li><a href="#aboutTab" class="reduce-font-size">About</a></li>
	</ul>
	<div id="webIRC" class="reduce-tab-padding">
		<iframe id='irc_frame' frameborder="0"></iframe><br>
	</div>
	<?php
		if($logged_in) { // If logged in, show spot form
		?>
	<div id="editStation" class="reduce-tab-padding">
		<h4>My Station Description:</h4>
		<input type=text name="station_description_edit" id="station_description_edit" />
	</div>
	<?php } ?>
	<div id="helpTab" class="reduce-tab-padding">
		You need to be registered to submit spots.
	</div>
	<div id="aboutTab" class="reduce-tab-padding">
		DXSpot.TV is an ATV Spotting Site...
	</div>
</div>
</td>
</tr>
</tbody>
</table>
<br>
Copyright 2013 Phil Crump <a href='https://www.thecraag.com/' target='_blank'>thecraag.com</a>
</div>
</body>
</html>
