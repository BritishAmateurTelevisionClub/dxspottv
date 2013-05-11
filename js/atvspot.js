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

function createUserMarker(user_data) {
	var lat_lon = new google.maps.LatLng(user_data['latitude'], user_data['longitude']);
	var contentString;
	if(user_data['months_active']>1) {
		contentString = 'Last active ' + user_data['months_active'] + ' months ago.';
	} else if(user_data['months_active']>0) {
		contentString = 'Last active ' + user_data['months_active'] + ' month ago.';
	} else if (user_data['days_active']>1) {
		contentString = 'Last active ' + user_data['days_active'] + ' days ago.';
	} else if (user_data['days_active']>0) {
		contentString = 'Last active ' + user_data['days_active'] + ' day ago.';
	} else if (user_data['hours_active']>1) {
		contentString = 'Last active ' + user_data['hours_active'] + ' hours ago.';
	} else if (user_data['hours_active']>0) {
		contentString = 'Last active ' + user_data['hours_active'] + ' hour ago.';
	} else {
		contentString = 'Currently Active.'
	}
	var marker = new google.maps.Marker({
        position: lat_lon,
		icon: userActiveIcon,
        map: map,
        title: user_data['callsign']
    });
    marker.callsign = user_data['callsign'];
    user_markers.push(marker);

	google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent("<b>"+user_data['callsign']+"</b><br>"+contentString);
        infowindow.open(map,marker);
   	});
}

function createRepeaterMarker(repeater_data) {
	var latlon = new google.maps.LatLng(repeater_data['latitude'], repeater_data['longitude']);
	if(repeater_data['active']==1) {
		var toBeIcon = repeaterIcon;
	} else {
		var toBeIcon = repeaterOfflineIcon;
	}
	var marker = new google.maps.Marker({
        position: latlon,
		icon: toBeIcon,
        map: map,
        title: repeater_data['callsign']
	});
    marker.callsign = repeater_data['callsign'];
    marker.is70cm = repeater_data['is_70cm'];
    marker.is23cm = repeater_data['is_23cm'];
    marker.is13cm = repeater_data['is_13cm'];
    marker.is3cm = repeater_data['is_3cm'];
    repeater_markers.push(marker);

	google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent("<b>"+repeater_data['callsign']+"</b><br>"+repeater_data['description']);
        infowindow.open(map,marker);
  	});
}

function parseRepeaters(JSONinput) {
	var r_id = new Array();
	for(r_id in JSONinput){
		var repeater = JSONinput[r_id];
		var marker_search = $.grep(repeater_markers, function(e){ return e.callsign == repeater['callsign']; });
		if(marker_search==0) {
			createRepeaterMarker(repeater);
		}
	}
}

function parseUsers(JSONinput) {
	var u_id = new Array();
	for(u_id in JSONinput){
		var user = JSONinput[u_id];
		var marker_search = $.grep(user_markers, function(e){ return e.callsign == user['callsign']; });
		if(marker_search==0) {
			createUserMarker(user);
		}
	}
}

