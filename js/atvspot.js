var repeater_markers = [];
var user_markers = [];
var spot_lines = [];
var map;

var infowindow;
var session_id;
var logged_in;

// Load Google Maps Script
//
$(document).ready(function() {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=geometry&sensor=false&callback=initialize'; // callback: initialize()
	document.body.appendChild(script);
});

// Callback from Google Maps Script Load
//
function initialize() {
	google.maps.visualRefresh = true;
	var mapOptions = {
		zoom: 6,
		center: new google.maps.LatLng(51.5, -1.39),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		streetViewControl: false
	};

	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
	infowindow = new google.maps.InfoWindow( {
			size: new google.maps.Size(150,50)
	});

	google.maps.event.addListener(map, 'click', function() {
		infowindow.close();
	});

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
	if(user_data['known']==0) {
		var toBeIcon = userUnknownIcon;
	} else if(user_data['seconds_active']>3600) { // 1 hour
		var toBeIcon = userAwayIcon;
	} else {
		var toBeIcon = userActiveIcon;
	}
	var marker = new google.maps.Marker({
        position: lat_lon,
		icon: toBeIcon,
        map: map,
        title: user_data['callsign']
    });
    marker.user_id = user_data['id']
    marker.callsign = user_data['callsign'];
    marker.is70cm = user_data['is_70cm'];
    marker.is23cm = user_data['is_23cm'];
    marker.is13cm = user_data['is_13cm'];
    marker.is3cm = user_data['is_3cm'];
    marker.activity = user_data['seconds_active'];
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
	marker.repeater_id = repeater_data['id'];
    marker.callsign = repeater_data['callsign'];
    marker.is70cm = repeater_data['is_70cm'];
    marker.is23cm = repeater_data['is_23cm'];
    marker.is13cm = repeater_data['is_13cm'];
    marker.is3cm = repeater_data['is_3cm'];
    repeater_markers.push(marker);
    
    var infoContent = ['<div id="tabs">',
      '<ul>',
        '<li><a href="#repeaterInfoTab"><span>Info</span></a></li>',
        '<li><a href="#repeaterDescTab"><span>Description</span></a></li>',
      '</ul>',
      '<div id="repeaterInfoTab">',
        '<p><b>', repeater_data['callsign'], '</b></p>',
      '</div>',
      '<div id="repeaterDescTab">',
       '<p>', repeater_data['description'], '</p>',
      '</div>',
      '</div>'].join('');

	google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent(infoContent);
        infowindow.open(map,marker);
  	});
}

function createSpotLine(spot_data) {

	var primary_search = $.grep(user_markers, function(e){ return e.user_id == spot_data['primary_id']; });
	var primary_latlon = primary_search[0].position;
	var primary_callsign = primary_search[0].callsign;
	if(spot_data['secondary_isrepeater']==0) {
		var secondary_search = $.grep(user_markers, function(e){ return e.user_id == spot_data['secondary_id']; });
	} else {
		var secondary_search = $.grep(repeater_markers, function(e){ return e.repeater_id == spot_data['secondary_id']; });
	}
	
	var secondary_latlon = secondary_search[0].position;
	var secondary_callsign = secondary_search[0].callsign;
	
	var spotLineCoordinates = [
		primary_latlon,
		secondary_latlon
	];
	var spotLine = new google.maps.Polyline({
    	path: spotLineCoordinates,
    	strokeColor: "#FF0000",
    	strokeOpacity: 1.0,
    	strokeWeight: 2,
    	geodesic: true
	});
	
	spotLine.spot_id = spot_data['id'];
	spotLine.frequency = spot_data['frequency'];
	spotLine.mode_id = spot_data['mode_id'];
	
	spotLine.primary_id = spot_data['primary_id'];
	spotLine.primary_callsign = primary_callsign;
	spotLine.secondary_id = spot_data['secondary_id'];
	spotLine.secondary_callsign = secondary_callsign;
	spotLine.secondary_isrepeater = spot_data['secondary_isrepeater']
	spotLine.time = spot_data['time'];
	spotLine.ago = spot_data['seconds_ago'];
	spotLine.comments = spot_data['comments'];
	spotLine.date = parseInt(spot_data['time'].substr(8,2))+"&nbsp;"+months[parseInt(spot_data['time'].substr(5,2))]+"&nbsp;"+spot_data['time'].substr(11,8);	
	spotLine.distance = Math.round((google.maps.geometry.spherical.computeDistanceBetween(primary_latlon, secondary_latlon)/1000)*10)/10;
	
	var infoContent = spotLine.date+"<br><b>"+primary_callsign+"</b>&nbsp;->&nbsp;"+"<b>"+secondary_callsign+"</b><br>"+spotLine.distance+"&nbsp;km&nbsp;"+spotLine.frequency+"&nbsp;MHz";
	
	google.maps.event.addListener(spotLine, 'click', function() {
		infowindow.setContent(infoContent);
		infowindow.setPosition(new google.maps.LatLng((primary_latlon.lat() + secondary_latlon.lat())/2, (primary_latlon.lng() + secondary_latlon.lng())/2));
    	infowindow.open(map);
   	});
	
	spotLine.setMap(map);
	spot_lines.push(spotLine);
}

function parseRepeaters(JSONinput) {
	var r_id = new Array();
	for(r_id in JSONinput){
		var repeater = JSONinput[r_id];
		var marker_search = $.grep(repeater_markers, function(e){ return e.callsign == repeater['callsign']; });
		if(marker_search.length==0 && repeater.length!=0) {
			createRepeaterMarker(repeater);
		}
	}
}

function parseUsers(JSONinput) {
	var u_id = new Array();
	for(u_id in JSONinput){
		var user = JSONinput[u_id];
		var marker_search = $.grep(user_markers, function(e){ return e.callsign == user['callsign']; });
		if(marker_search.length==0 && user.length!=0) {
			createUserMarker(user);
		}
	}
}

function parseSpots(JSONinput) {
	var s_id = new Array();
	for(s_id in JSONinput){
		var spot = JSONinput[s_id];
		var spot_search = $.grep(spot_lines, function(e){ return e.spot_id == spot['id']; });
		if(spot_search.length==0 && spot.length!=0) {
			createSpotLine(spot);
		}
	}
}
