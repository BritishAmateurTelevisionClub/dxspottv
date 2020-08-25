<?php
session_start();

if (isset($_COOKIE["auth_error"]))
{
    if ($_COOKIE["auth_error"]=="1")
    {
        // Unset Auth Error
        setcookie("auth_error", "", time()-3600);
        // Redirect back after failed login
        $user_known = 0;
        $logged_in = 0;
        $auth_error = 1;
        $auth_error_text = $_COOKIE["auth_error_text"];
    } 
    else 
    {
        require('dxspottv_pdo.php');
        $callsign_statement = $dbc->prepare("SELECT callsign,name FROM users WHERE id=?;");
        $callsign_statement->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
        $callsign_statement->execute();
        $callsign_statement->bindColumn(1, $callsign);
        $callsign_statement->bindColumn(2, $name);
        $callsign_statement->fetch();
        // Logged in, but check session id is valid
        $sessions_statement = $dbc->prepare("SELECT session_id FROM sessions WHERE user_id=? ORDER BY activity DESC LIMIT 1;");
        $sessions_statement->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
        $sessions_statement->execute();
        $sessions_statement->bindColumn(1, $sessions_result);
        if($sessions_statement->rowCount()==0) 
        { // session doesn't exist on server
            $user_known = 1;
            $logged_in = 0;
            $auth_error = 1;
            $auth_error_text = "Session not found, please log in.";
        } 
        else 
        {
            $logged_in = 0;
            while ($sessions_statement->fetch()) 
            {
                if ($_COOKIE["session_key"]==$sessions_result) 
                { // Session matches, so is logged in!
                    $user_known = 1;
                    $logged_in = 1;
                    $auth_error = 0;
                }
            }
            if($logged_in != 1) 
            {
                // Session doesn't match, make them log in again
                $user_known = 1;
                $auth_error = 1;
                $auth_error_text = "Session not found, please log in.";
            }
        }
    $auth_error=0;
    }
}
else
{
    // Guest User
    $user_known = 0;
    $logged_in = 0;
    $auth_error = 0;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="description" content="Easy to use DX Spotting for ATV/DATV contacts on all bands: 70cms - 3cms.">
<meta name="keywords" content="dxspot,atv,tv,spot,dx,cluster,contacts,ham,amateur,television,chat,irc">
<title>DXSpot.TV</title>
<link href="/lib/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet">
<link href="/css/atvspot.css" rel="stylesheet">
<script type="text/javascript">
<?php if($user_known) { ?> // Do we fill in callsign as nick for irc
    var irc_frame_source = "/dxchat/?room=1&nick=<?php print $name . "_" . $callsign; ?>";
<?php } ?>
var logged_in = <?php if($logged_in) { print 'true'; } else { print 'false'; } ?>;
var auth_error = <?php if($auth_error==1) { print 'true'; } else { print 'false'; } ?>;
var auth_error_text = "<?php if($auth_error==1) { print $auth_error_text; } ?>";
</script>
<script src="/lib/jquery-3.2.1.min.js"></script>
<script src="/lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<script src="/lib/infobubble.min.js"></script>
<script src="https://www.google.com/jsapi"></script>
<script src="/js/atvspot.js"></script>
<script src="/js/atvspot-ajax.js"></script>
<script src="/js/atvspot-ui.js"></script>
<script src="/js/atvspot-util.js"></script>
<script src="/js/locator.js"></script>
<script src="/js/map.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA72exT_7wxeWSxakb3hEFVgnWqmv6mx1A&libraries=geometry&callback=init_map" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/cookie-bar/cookiebar-latest.min.js"></script>
</head>
<body>
<div style="text-align: center; align: top; height: 100%; ">
<div id="page-banner"><img src="/images/dxspot-header-logos.png" style="height: 80px;" /></div>
<table
style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;"
border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td width="55%" style="padding: 5px; vertical-align: top;">

<form action="#" style="margin-bottom: 0.5em;">
<table border="0" cellspacing="3" cellpadding="0" width="100%" >
<tr>

<td class="mapSelecter">
<strong>Filter Spots:</strong>&nbsp;

<select id="band_select">
  <option value="all">All Bands</option>
  <option value="2m" style="background-color: #FF0000; color: white;">2m</option>
  <option value="70cm" style="background-color: #FF0000; color: white;">70cm</option>
  <option value="23cm" style="background-color: #FFA500; color: white;">23cm</option>
  <option value="13cm" style="background-color: #FFA500; color: white;">13cm</option>
  <option value="9cm" style="background-color: #FFA500; color: white;">9cm</option>
  <option value="6cm" style="background-color: #FFA500; color: white;">6cm</option>
  <option value="3cm" style="background-color: #0404B4; color: white;">3cm</option>
  <option value="1.2cm" style="background-color: #0404B4; color: white;">1.2cm</option>
</select>
&nbsp;
<select id="time_select">
  <option value="6months">Last 6 Months</option>
  <option value="1month">Last Month</option>
  <option value="1week">Last Week</option>
  <option value="24hours" selected="selected">Last 24 Hours</option>
  <option value="12hours">Last 12 Hours</option>
  <option value="6hours">Last 6 Hours</option>
</select>

<td class="mapSelecter">
<strong>Show repeaters:</strong>&nbsp;<input type="checkbox" id="repeaterBox" />
</td>

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
    <option value=7>2m</option>
    <option value=1>70cm</option>
    <option value=2>23cm</option>
    <option value=3>13cm</option>
    <option value=5>9cm</option>
    <option value=6>6cm</option>
    <option value=4>3cm</option>
    <option value=8>1.2cm</option>
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
&nbsp;<a class="reduce-font-size" href="/resetPassword/" target="_blank">Forgotten Password?</a>
</div>
<div class="ui-state-error ui-corner-all reduce-font-size" id="auth-error-box">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
        <strong>Alert:</strong>&nbsp;<span id="auth-error-text"></span>
    </p>
</div>
<?php
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
        <b>Enter station callsign:&nbsp;</b><input type=text id="search_callsign" class="spot_box_short" /><button class="search-button reduce-font-size" id="search_button">Search</button>
        <br><br>
        <div id="findResults">
        </div>
    </div>
    <div id="helpTab" class="reduce-tab-padding">
        Download the user guide from here <a href="/dxspotguidev1.pdf" target="_blank">English (PDF)</a> <a href="http://www.pi6ats.nl/Gebruiksaanwijzing%20DXspot.pdf">Dutch (PDF)</a><br>
        <li>Thanks to Chris PA3CRX for the Dutch Translation</li>
        <br>
        Online help is available at the dxspot.tv forum at <a href="http://www.batc.org.uk/forum/viewforum.php?f=80" target="_blank">BATC Forums</a><br>
        <br>
        Or you can email Phil at <a href="mailto:phil@philcrump.co.uk">phil@philcrump.co.uk</a>
        <br>
        <br>
        <u>Map key</u><br>
        <br>
        Station Icons:<br>
        Green = logged in and radio active<br>
        Yellow = logged in but just watching - set by tick box in "Edit my station" <br>
        White = Not logged in but spotted on a Dx contact<br>
        <br>
        Cyan = Operational repeater<br>
        Red = Licensed but non operational repeater <br>
        <br>
        <u>DxSpot key </u><br>
        <br>
        Red = 70cms<br>
        Orange = 23cms<br>
        Blue = 13cms and above<br>
        <br>
        Dotted line = Narrow band beacon spot<br>

    </div>
    <div id="aboutTab" class="reduce-tab-padding">
        DXSpot.TV is an open development project, hosted at <a href="https://github.com/BritishAmateurTelevisionClub/dxspottv/">GitHub</a>. Contribution is welcome!
        <br><br>
        Most of the development has been done by Phil M0DNY, from concept by Noel G8GTZ.
        <br><br>
        Comments/suggestions are welcome, either to us on the Chat, or emailed to Phil at <a href="mailto:phil@philcrump.co.uk">phil@philcrump.co.uk</a>
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
      <b>Include Earth Curvature:</b>&nbsp;<input type="checkbox" id="curvatureBox" /><br>
</div>
<div id="elevationChart">
</div>
</div>
</body>
</html>
