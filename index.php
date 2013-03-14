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
<title>ATVSpot</title>
<link href="map-default.css" rel="stylesheet">
<?php include_once("g_analytics.php") ?>
<script type="text/javascript" src="js/map.js"></script>
<script>
	if(!Array.prototype.last) {
	    Array.prototype.last = function() {
	        return this[this.length - 1];
	    }
	}
	var repeater_markers = [];
	var user_markers = [];
        var map;

	var infowindow;
	var session_id;
	var logged_in;
	function initialize() {
		var mapOptions = {
        		zoom: 6,
        		center: new google.maps.LatLng(51.5, -1.39),
        		mapTypeId: google.maps.MapTypeId.ROADMAP
        	};

       		map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		infowindow = new google.maps.InfoWindow(
		{
			size: new google.maps.Size(150,50)
		});

		google.maps.event.addListener(map, 'click', function() {
		        infowindow.close();
	        });

		blueIcon = new google.maps.MarkerImage("https://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png");
		redIcon = new google.maps.MarkerImage("https://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png");

		getRepeaters();
		getUsers();

		show("user");
		show("70cm");
		show("23cm");
		show("13cm");
		show("3cm");
      	}

	function getMarkerImage(iconColor) {
   		if ((typeof(iconColor)=="undefined") || (iconColor==null)) {
			iconColor = "red";
		}
		if (!gicons[iconColor]) {
			gicons[iconColor] = new google.maps.MarkerImage("http://admissions.mansfield.edu/more/visit-mansfield/interactive-map/map/maps/pin-"+ iconColor +"2.png",
				// This marker is 20 pixels wide by 34 pixels tall.
				new google.maps.Size(30, 30),
				// The origin for this image is 0,0.
				new google.maps.Point(0,0),
				// The anchor for this image is at 6,20.
				new google.maps.Point(9, 30));
		}
		return gicons[iconColor];
	}

	function createUserMarker(latlng,name,html,category) {
		var contentString = html;
		var marker = new google.maps.Marker({
		        position: latlng,
			icon: redIcon,
			//icon: mapicons[category],
		        map: map,
		        title: name
        		//zIndex: Math.round(latlng.lat()*-100000)<<5
	        });
	        marker.mycategory = category;
	        marker.myname = name;
	        user_markers.push(marker);

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent(name+"<br>"+contentString);
		        infowindow.open(map,marker);
        	});
	}

	function createRepeaterMarker(latlng,name,html,category) {
		var contentString = html;
		var marker = new google.maps.Marker({
		        position: latlng,
			icon: blueIcon,
			//icon: mapicons[category],
		        map: map,
		        title: name
        		//zIndex: Math.round(latlng.lat()*-100000)<<5
	        });
	        marker.mycategory = category;
	        marker.myname = name;
	        repeater_markers.push(marker);

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent(name+"<br>"+contentString);
		        infowindow.open(map,marker);
        	});
	}

	function parseRepeaters(JSONinput) {
		var r_id = new Array();
		for(r_id in JSONinput){
			var repeater = JSONinput[r_id];
			createRepeaterMarker(new google.maps.LatLng(repeater['latitude'], repeater['longitude']),repeater['callsign'],repeater['description'],repeater['band']);
		}
    	}

	function parseUsers(JSONinput) {
		var u_id = new Array();
		for(u_id in JSONinput){
			var user = JSONinput[u_id];
			createUserMarker(new google.maps.LatLng(user['latitude'], user['longitude']),user['callsign'],"Last Active "+user['active']+" hours ago.","users");
		}
    	}

	function getRepeaters() {
	var JsonObject = {};
	var http = new XMLHttpRequest();
	http.open("GET", "/atvspot/ajax/repeaters.php", true);
	http.onreadystatechange = function () {
	   if (http.readyState == 4 && http.status == 200) {
    		var responseTxt = http.responseText;
    		myJSONObject = eval('(' + responseTxt + ')');
    		parseRepeaters(myJSONObject);
 	   }
	}
	http.send(null);
	}

	function getUsers() {
	var JsonObject = {};
	var http = new XMLHttpRequest();
	http.open("GET", "/atvspot/ajax/users.php", true);
	http.onreadystatechange = function () {
	   if (http.readyState == 4 && http.status == 200) {
    		var responseTxt = http.responseText;
    		myJSONObject = eval('(' + responseTxt + ')');
    		parseUsers(myJSONObject);
 	   }
	}
	http.send(null);
	}

      function loadScript() {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
            'callback=initialize';
        document.body.appendChild(script);
      }

      window.onload = loadScript;
</script>
</head>
<body>
<div style="text-align: center; align: top; height: 100%; ">
<h2>ATV Spot Map</h2>
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
<strong>Users:</strong> <input type="checkbox" id="userbox" onClick="boxclick(this,'user')" /></p></td>

<td><p align="center">
<strong>70cm:</strong> <input type="checkbox" id="70cmbox" onClick="boxclick(this,'70cm')" /></p></td>

<td><p align="center">
<strong>23cm:</strong> <input type="checkbox" id="23cmbox" onClick="boxclick(this,'23cm')" /></p></td>

<td><p align="center">
<strong>13cm:</strong> <input type="checkbox" id="13cmbox" onClick="boxclick(this,'13cm')" /></p></td>

<td><p align="center">
<strong>3cm:</strong> <input type="checkbox" id="3cmbox" onClick="boxclick(this,'3cm')" /></p></td>

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

</tr></table>
</form>


<div id="map_canvas"></div>
<?php
if($logged_in) { // If logged in, show spot form
?>
<h3>Spot</h3>
<form id='spot'>
Frequency: <input type=text></input>

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
<td width="40%" style="padding: 5px; vertical-align: top;">
<?php
if($logged_in) {
  print "Hi " . $callsign . "!";
?>
&nbsp;&nbsp;<a href="logout.php">Logout</a>
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
}
?>
</form>
<?php
} // End of greeting/login form
if($user_known) { // Do we fill in callsign as nick for irc
?>
<iframe src="https://webchat.freenode.net/?channels=#atvspot&nick=<?php print $callsign; ?>&prompt=1" frameborder="0" height="600px" width="100%"></iframe><br>
<?php
} else {
?>
<iframe src="https://webchat.freenode.net/?channels=#atvspot" frameborder="0" height="600px" width="100%"></iframe><br>
<?php
} // End of callsign as nick for irc
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

