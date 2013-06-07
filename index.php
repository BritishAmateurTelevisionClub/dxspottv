<?php
session_start();
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
    require('spot_login.php');
    $callsign_result = mysqli_query($dbc, "SELECT callsign,name FROM users WHERE id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));
    $callsign_row = mysqli_fetch_array($callsign_result);
    $callsign = $callsign_row["callsign"];
    $name = $callsign_row["name"];
    // Logged in, but check session id is valid
    $sessions_result = mysqli_query($dbc, "SELECT session_id FROM sessions WHERE user_id='" . $_COOKIE["user_id"] . "';") or die(mysqli_error($dbc));  
    if(mysqli_num_rows ($sessions_result)==0) { // session doesn't exist on server
      $user_known = 1;
      $logged_in = 0;
      $auth_error = 1;
      $auth_error_text = "Session not found, please log in.";
    } else {
      while($target_row = mysqli_fetch_array($sessions_result)) { // find a matching session
		  if ($_COOKIE["session_key"]==$target_row["session_id"]) {
		    // Session matches, so is logged in!
		    $user_known = 1;
		    $logged_in = 1;
		    $auth_error = 0;
		  }
      }
      if($logged_in != 1) {
        // Session doesn't match, make them log in again
        $user_known = 1;
        $logged_in = 0;
        $auth_error = 1;
        $auth_error_text = "Session not found, please log in.";
      }
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
<!DOCTYPE html>
<html>
<head>
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
<meta name="description" content="Easy to use DX Spotting for ATV/DATV contacts on all bands: 70cms - 3cms.">
<meta name="keywords" content="dxspot,atv,tv,spot,dx,cluster,contacts,ham,amateur,television,chat,irc">
<title>DXSpot.TV</title>
<link href="css/atvspot.css" rel="stylesheet">
<link href="css/flick/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script type="text/javascript">
<?php if($user_known) { ?> // Do we fill in callsign as nick for irc
	var irc_frame_source = "http://webirc.dxspot.tv/?channels=#dxspottv&nick=<?php print $name . "_" . $callsign; ?>";
<?php } // End of callsign as nick for irc
if($logged_in) { ?>
	var logged_in = true;
<?php } else { ?>
	var logged_in = false;
<?php } ?>
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="/js/jquery-plus-ui.js"></script>
<script src="/js/atvspot-combined.min.js"></script>
<script src="/js/infobubble.min.js"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-37815608-6', 'dxspot.tv');
  ga('send', 'pageview');
</script>
</head>
<body>
<div style="text-align: center; align: top; height: 100%; ">
<img src="/images/DXS8.jpg" style="height: 79px; width: 179px;" />
<table
style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;"
border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td width="55%" style="padding: 5px; vertical-align: top;">

<form action="#" style="margin-bottom: 0.5em;">
<table border="0" cellspacing="3" cellpadding="0" width="100%" >
<tr>

<td><p class="mapSelecter" align="center">
<strong>Timespan:</strong>&nbsp;
<select id="time_select">
  <option value="all">All Spots</option>
  <option value="year">Last year</option>
  <option value="6months">Last 6 months</option>
  <option value="1month">Last Month</option>
  <option value="1week">Last Week</option>
  <option value="24hours" selected="selected">Last 24 Hours</option>
  <option value="12hours">Last 12 Hours</option>
  <option value="6hours">Last 6 Hours</option>
</select>
</td>

<td><p class="mapSelecter" align="center">
<strong>Band:</strong>&nbsp;
<select id="band_select">
  <option value="all">All Bands</option>
  <option value="70cm">70cm</option>
  <option value="23cm">23cm</option>
  <option value="13cm">13cm</option>
  <option value="9cm">9cm</option>
  <option value="6cm">6cm</option>
  <option value="3cm">3cm</option>
</select>

<td><p class="mapSelecter" align="center">
<strong>Show repeaters:</strong>&nbsp;<input type="checkbox" id="repeaterBox" /></p></td>

</tr></table>
</form>
<div id="map_canvas"></div>
<?php if($logged_in) { // If logged in, show spot form ?>
<table id="spot_table">
<tr>
<td id="spot_log_cell">
	<div id="spot_log_div" style="line-height: 0.9em;">
		<h4>Global Spot Log</h4>
		<span id="spotLog" class="reduce-font-size">Loading...</span>
	</div>
</td><td id="spot_form_cell">
	<b>New Spot</b><br>
	<select id="spot_band_select">
	<option value=1 style="background-color: #FF0000; color: white;">70cm</option>
	<option value=2 style="background-color: #FFA500; color: white;">23cm</option>
	<option value=3 style="background-color: #0404B4; color: white;">13cm</option>
	<option value=5 style="background-color: #0404B4; color: white;">9cm</option>
	<option value=6 style="background-color: #0404B4; color: white;">6cm</option>
	<option value=4 style="background-color: #0404B4; color: white;">3cm</option>
	</select>
	&nbsp;
	<select id="spot_mode_select">
	<option value="1">Analog TV</option>
	<option value="2">Digital TV</option>
	<option value="3">Beacon</option>
	</select><br>
	<b>Remote</b>&nbsp;Callsign:&nbsp;&nbsp;<input type=text name="remote_callsign" id="remote_callsign" class="spot_box_short" />
	<br><span class="spotFormLabel">Locator:</span>&nbsp;&nbsp;<input type=text name="remote_loc" id="remote_loc" class="spot_box_short" /><br>
	Frequency / Comments:<br><input type=text name="spot_comments" id="spot_comments" class="spot_box_long" /><br>
	<button class="spot-button reduce-font-size" id="spot_button">Submit Spot</button>&nbsp;<span id="submitStatus"></span>
</td></tr></table>
<?php } else { ?>
<div id="spot_wide_log_div" style="line-height: 0.9em;">
	<h4>Global Spot Log</h4>
	<span id="spotLog" class="reduce-font-size">Loading...</span>
</div>
<?php } ?>
</td>
<td width="45%" style="padding: 5px; vertical-align: top;">
<?php
if($logged_in) {
?>
<div style="padding-bottom: 5px;">
You are logged in as <?php print $callsign; ?>&nbsp;&nbsp;<button class="logout-button reduce-font-size" id="logout_button">Logout</button>
</div>
<?php
} else {
?>
<div style="padding-bottom: 5px;">
Callsign: <input type=text name="callsign" id="callsign_input" <?php if($user_known) { print 'value="' . $callsign . '"'; } ?>/>
&nbsp;Password: <input type=password name="passwd" id="passwd_input" />
&nbsp;&nbsp;<button class="login-button reduce-font-size" id="login_button">Log In</button>
&nbsp;&nbsp;<button class="register-button reduce-font-size" id="register_button">Register</button>
</div>
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
		<li><a href="#webIRC" class="reduce-font-size">DXSpot Chat</a></li>
		<?php
		if($logged_in) { // If logged in, show spot form
		?>
		<li><a href="#editStation" class="reduce-font-size">Edit My Station</a></li>
		<?php } ?>
		<li><a href="#findStation" class="reduce-font-size">Find Station</a></li>
		<li><a href="#helpTab" class="reduce-font-size">Help</a></li>
		<li><a href="#aboutTab" class="reduce-font-size">About</a></li>
	</ul>
	<div id="webIRC" class="reduce-tab-padding">
		<?php if($logged_in) { ?>
			<iframe id='irc_frame' frameborder="0"></iframe><br>
		<?php } else { ?>
			<div id='n_irc_content'>
				<h2>Welcome to DXSpot.TV</h2>
				<h3>Please Register and Log In to use the ATV DXSpot Chat and Submit Spots.</h3>
			</div>
		<?php } ?>
	</div>
	<?php
		if($logged_in) { // If logged in, show edit station
		?>
	<div id="editStation" class="reduce-tab-padding">
		<h4>My Station Description:</h4>
		<b>I am currently active on ATV:</b>&nbsp;<input type="checkbox" id="radioBox" /><span id="changeRadioStatus"></span><br>
		<textarea rows="4" cols="50" id="station_description_edit"></textarea><br>
		No HTML permitted.<br><br>
		<b>Website:</b><br>
		http://<input type="text" id="station_website_edit"></input>
		<br><br>
		<b>Location</b><button class="reduce-font-size" id="setposition_button">Set With Map</button>&nbsp;<span id="changePosStatus"></span><br>
		Latitude: <input type="text" id="station_lat_edit"></input><br>
		Longitude: <input type="text" id="station_lon_edit"></input><br>
		<br>
		<button class="station-desc-button" id="desc_button">Save</button>&nbsp;<span id="changeDescStatus"></span>
	</div>
	<?php } ?>
	<div id="findStation" class="reduce-tab-padding">
		<b>Search:&nbsp;</b><input type=text id="search_callsign" class="spot_box_short" /><button class="search-button reduce-font-size" id="search_button">Search</button>
		<br><br>
		<div id="findResults">
		</div>
	</div>
	<div id="helpTab" class="reduce-tab-padding">
		Download the user guide from here <a href="http://www.dxspot.tv/User_Guide_C.pdf" target="_blank">User Guide Issue C (PDF)</a><br>
		<br>
		Online help is available at the dxspot.tv forum at <a href="http://www.batc.org.uk/forum/viewforum.php?f=80" target="_blank">BATC Forums</a><br>
		<br>
		Or you can email us at <a href="mailto:dxspottv.feedback@gmail.com">dxspottv.feedback@gmail.com</a>

	</div>
	<div id="aboutTab" class="reduce-tab-padding">
		DXSpot.TV is an open development environment project using github.  
		If you would like to contribute please email us at 
		<a href="mailto:dxspottv.feedback@gmail.com">dxspottv.feedback@gmail.com</a><br>
		<br>
		All the hard work so far has been done by Phil, M0DNY, from an idea by Noel, G8GTZ.

	</div>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div id="elevationDialog" title="Elevation Profile">
<div style="margin-left: 2px; line-height: 1.5em;">
      <b>From:</b>&nbsp;<span id='spanChartFrom'></span><br>
      <b>To:</b>&nbsp;<span id='spanChartTo'></span><br>
</div>
<div id="elevationChart">
</div>
</div>
</body>
</html>
