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
		center: new google.maps.LatLng(52.5, -1.25),
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

	getMapData();
}

function createUserMarker(user_data) {
	var lat_lon = new google.maps.LatLng(user_data['latitude'], user_data['longitude']);
	var contentString = activityString(user_data);
	
	if(user_data['known']==0) {
		var toBeIcon = userUnknownIcon;
	} else if(user_data['seconds_active']>40) { // 40 seconds, should check in every 8 seconds
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
    marker.locator = user_data['locator'];
    marker.activity = user_data['seconds_active'];
    marker.known = user_data['known'];
    user_markers.push(marker);
    
    if(marker.known=="1") {
    	var contentString = "<b>"+user_data['callsign']+"</b><br>"+activityString(user_data);
    } else {
    	var contentString = "<b>"+user_data['callsign']+"</b><br>";
    }

	google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent(contentString);
        infowindow.open(map,marker);
   	});
}

function updateUserMarker(user_data, user_index) {
	if(user_data['known']==0) {
		user_markers[user_index].setIcon(userUnknownIcon);
	} else if(user_data['seconds_active']>40) { // 40 seconds, should check in every 8 seconds
		user_markers[user_index].setIcon(userAwayIcon);
	} else {
		user_markers[user_index].setIcon(userActiveIcon);
	}

    user_markers[user_index].activity = user_data['seconds_active'];
    
    if(user_data.known=="1") {
    	var contentString = "<b>"+user_data['callsign']+"</b><br>"+activityString(user_data);
    } else {
    	var contentString = "<b>"+user_data['callsign']+"</b><br>";
    }
    google.maps.event.clearListeners(user_markers[user_index], 'click');
	google.maps.event.addListener(user_markers[user_index], 'click', function() {
		infowindow.setContent(contentString);
        infowindow.open(map,user_markers[user_index]);
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
    marker.qth_r = repeater_data['qth_r'];
    marker.qth = repeater_data['qth'];
    marker.tx_freq = repeater_data['tx_freq'];
    marker.rx_freq = repeater_data['rx_freq'];
    if (typeof repeater_data['rx_freq_2'] != 'undefined') {
    	marker.rx_freq_2 = repeater_data['rx_freq_2'];
    }
    if (typeof repeater_data['alt_tx_freq'] != 'undefined') {
    	marker.alt_tx_freq = repeater_data['alt_tx_freq'];
    	marker.alt_rx_freq = repeater_data['alt_rx_freq'];
    	if (typeof repeater_data['alt_rx_freq_2'] != 'undefined') {
    		marker.alt_rx_freq_2 = repeater_data['alt_rx_freq_2'];
    	}
    }
    marker.is70cm = repeater_data['is_70cm'];
    marker.is23cm = repeater_data['is_23cm'];
    marker.is13cm = repeater_data['is_13cm'];
    marker.is9cm = repeater_data['is_9cm'];
    marker.is6cm = repeater_data['is_6cm'];
    marker.is3cm = repeater_data['is_3cm'];
    repeater_markers.push(marker);
    
    var infoTab = '<div id="content">'+
        '<h4>'+marker.callsign+'</h4>'+
        '<b>'+marker.qth_r+'</b>&nbsp;-&nbsp;'+marker.qth;
    if(logged_in) {
    	var user_latlng = new google.maps.LatLng(user_lat, user_lon);
    	infoTab+='<br><br>'+
    		'<b>Bearing:</b>&nbsp;'+'<br>'+convertHeading(google.maps.geometry.spherical.computeHeading(user_latlon, latlon))+'&deg;';
    		'<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlon, latlon)/1000)*10)/10+'km';
    }
    infoTab += '</div>';
    var freqTab = '<div id="content">'+
    	'<b>TX:&nbsp;'+marker.tx_freq+'MHz</b><br>'+
    	'<b>RX:&nbsp;'+marker.rx_freq+'MHz</b><br>';
    if (typeof marker.rx_freq_2 != 'undefined') {
    	freqTab += '<b>RX:&nbsp;'+marker.rx_freq_2+'MHz</b><br>';
    }
    if (typeof marker.alt_tx_freq != 'undefined') {
    	freqTab += '<br><b>TX:&nbsp;'+marker.alt_tx_freq+'MHz</b><br>'+
    		'<b>RX:&nbsp;'+marker.alt_rx_freq+'MHz</b><br>';
    	if (typeof marker.rx_freq_2 != 'undefined') {
			freqTab += '<b>RX:&nbsp;'+marker.alt_rx_freq_2+'MHz</b><br>';
		}
    }
    freqTab += '</div>';
    var descTab = '<div id="content">'+
        '<h1>content2</h1>'+
        '</div>';
    
    var infoBubble = new InfoBubble({
        maxWidth: 200,
        minWidth: 200,
        maxHeight: 150,
        minHeight: 150,
		shadowStyle: 0,
		padding: 8,
		backgroundColor: '#fff',
		borderRadius: 8,
		arrowSize: 10,
		borderWidth: 1,
		borderColor: '#ccc',
		disableAutoPan: true,
		hideCloseButton: false,
		arrowPosition: 50,
		arrowStyle: 0
    });
  	
    infoBubble.addTab('Info', infoTab);
    infoBubble.addTab('In/Out', freqTab);
    infoBubble.addTab('Description', descTab);

    google.maps.event.addListener(marker, 'click', function() {
        if (!infoBubble.isOpen()) {
            infoBubble.open(map, marker);
        }
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
	spotLine.band_id = spot_data['band_id'];
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
	
	var infoContent = spotLine.date+"<br><b>"+primary_callsign+"</b>&nbsp;->&nbsp;"+"<b>"+secondary_callsign+"</b><br>"+bandFromID(spotLine.band_id)+"&nbsp;"+spotLine.distance+"&nbsp;km<br><i>"+spotLine.comments+"</i>";
	
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
		if(user.length!=0) {
			var marker_search = $.grep(user_markers, function(e){ return e.callsign == user['callsign']; });
			if(marker_search.length==0) {
				createUserMarker(user);
			} else {
			    user_index = $.inArray(marker_search[0], user_markers);
				updateUserMarker(user, user_index);
			}
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

function activityString(user_data) {
    var activeString;
	if(user_data['months_active']>1) {
		activeString = 'Last active ' + user_data['months_active'] + ' months ago.';
	} else if(user_data['months_active']>0) {
		activeString = 'Last active ' + user_data['months_active'] + ' month ago.';
	} else if (user_data['days_active']>1) {
		activeString = 'Last active ' + user_data['days_active'] + ' days ago.';
	} else if (user_data['days_active']>0) {
		activeString = 'Last active ' + user_data['days_active'] + ' day ago.';
	} else if (user_data['hours_active']>1) {
		activeString = 'Last active ' + user_data['hours_active'] + ' hours ago.';
	} else if (user_data['hours_active']>0) {
		activeString = 'Last active ' + user_data['hours_active'] + ' hour ago.';
	} else if (user_data['seconds_active']>300) {
		activeString = 'Last active ' + Math.round(user_data['seconds_active']/60) + ' minutes ago.';
	} else {
		activeString = 'Currently Active.';
	}
	return activeString;
}
