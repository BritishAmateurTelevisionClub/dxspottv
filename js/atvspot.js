var repeater_markers = [];
var user_markers = [];
var map;

var infowindow;
var session_id;
var logged_in;

// Load Google Maps Script
//
$(document).ready(function() {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=initialize'; // callback: initialize()
	document.body.appendChild(script);
});

// Callback from Google Maps Script Load
//
function initialize() {
	var mapOptions = {
		zoom: 6,
		center: new google.maps.LatLng(51.5, -1.39),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
	infowindow = new google.maps.InfoWindow( {
			size: new google.maps.Size(150,50)
	});

	google.maps.event.addListener(map, 'click', function() {
		infowindow.close();
	});

	blueIcon = new google.maps.MarkerImage("https://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png");
	redIcon = new google.maps.MarkerImage("https://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png");

	userActiveIcon = new google.maps.MarkerImage("/images/active_user.ico");
	userAwayIcon = new google.maps.MarkerImage("/images/away_user.ico");
	userUnknownIcon = new google.maps.MarkerImage("/images/unknown_user.ico");
	repeaterIcon = new google.maps.MarkerImage("/images/active_repeater.ico");
	repeaterOfflineIcon = new google.maps.MarkerImage("/images/inactive_repeater.ico");

	getRepeaters();
	getUsers();

	mapShow("user");
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
				icon: userActiveIcon,
				//icon: mapicons[category],
		        map: map,
		        title: name
        		//zIndex: Math.round(latlng.lat()*-100000)<<5
	        });
	        marker.mycategory = category;
	        marker.myname = name;
	        user_markers.push(marker);

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent("<b>"+name+"</b><br>"+contentString);
		        infowindow.open(map,marker);
        	});
	}

	function createRepeaterMarker(latlng,name,html,category,active) {
		var contentString = html;
		if(active==1) {
			var toBeIcon = repeaterIcon;
		} else {
			var toBeIcon = repeaterOfflineIcon;
		}
		var marker = new google.maps.Marker({
		        position: latlng,
				icon: toBeIcon,
				//icon: mapicons[category],
		        map: map,
		        title: name
        		//zIndex: Math.round(latlng.lat()*-100000)<<5
	        });
	        marker.mycategory = category;
	        marker.myname = name;
	        repeater_markers.push(marker);

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent("<b>"+name+"</b><br>"+contentString);
		        infowindow.open(map,marker);
        	});
	}

	function parseRepeaters(JSONinput) {
		var r_id = new Array();
		for(r_id in JSONinput){
			var repeater = JSONinput[r_id];
			createRepeaterMarker(new google.maps.LatLng(repeater['latitude'], repeater['longitude']),repeater['callsign'],repeater['description'],repeater['band'],repeater['active']);
		}
    	}

	function parseUsers(JSONinput) {
		var u_id = new Array();
		for(u_id in JSONinput){
			var user = JSONinput[u_id];
			var activity_str;
			if(user['months_active']>1) {
				activity_str = 'Last active ' + user['months_active'] + ' months ago.';
			} else if(user['months_active']>0) {
				activity_str = 'Last active ' + user['months_active'] + ' month ago.';
			} else if (user['days_active']>1) {
				activity_str = 'Last active ' + user['days_active'] + ' days ago.';
			} else if (user['days_active']>0) {
				activity_str = 'Last active ' + user['days_active'] + ' day ago.';
			} else if (user['hours_active']>1) {
				activity_str = 'Last active ' + user['hours_active'] + ' hours ago.';
			} else if (user['hours_active']>0) {
				activity_str = 'Last active ' + user['hours_active'] + ' hour ago.';
			} else {
				activity_str = 'Currently Active.'
			}
			createUserMarker(new google.maps.LatLng(user['latitude'], user['longitude']),user['callsign'],activity_str,"users");
		}
}

