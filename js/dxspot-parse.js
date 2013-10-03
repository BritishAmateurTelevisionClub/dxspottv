var repeater_markers = [];
var user_markers = [];
var spot_lines = [];

var months = ["_dummy_", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

function initialLoad() {
	loadUsers(initData['users']);
    parseRepeaters(initData['repeaters']);
    parseSpots(initData['spots']);
    createGlobalSpotLog(initData['spots']);
    initData = null;
	checkSpots();
	checkUsers();
	checkRepeaters();
}

function createUserMarker(user_data) {
	var lat_lon = new google.maps.LatLng(user_data['lat'], user_data['lon']);
	
	var marker = new google.maps.Marker({
        position: lat_lon,
        map: map,
        title: user_data['callsign']
    });
	
	if(user_data['seconds_active']>18) { // 18 seconds, should check in every 5 seconds
		marker.setOptions( {
			icon: userUnknownIcon, // white icon, if shown (spotted)
			zIndex: 11
		});
	} else if(user_data['radio_active']==1) {
		marker.setOptions( {
			icon: userActiveIcon, // green
			zIndex: 13
		});
	} else {
		marker.setOptions( {
			icon: userAwayIcon, // yellow
			zIndex: 12
		});
	}
	
    marker.user_id = user_data['id']
    marker.callsign = user_data['callsign'];
    marker.locator = user_data['locator'];
    marker.activity = user_data['seconds_active'];
    marker.known = user_data['known'];
    marker.station_desc = user_data['station_desc'];
    if(user_data['website']!='') {
    	marker.station_website = "http://"+user_data['website'];
    } else {
    	marker.station_website = '';
    }
    user_markers.push(marker);
    
    var infoTab = '<div class="user_bubble_info">'+
        '<h3 style="line-height: 0.3em;">'+marker.callsign+'</h3>'+
        '<b>'+marker.locator+'</b>';
    if(logged_in && (user_callsign!=user_data['callsign'])) {
    	var user_latlng = new google.maps.LatLng(user_lat, user_lon);
    	var elevation_vars = "'"+user_callsign+"','"+user_lat+"','"+user_lon+"','"+user_data['callsign']+"','"+user_data['lat']+"','"+user_data['lon']+"'";
    	infoTab+='<br><br>'+
    		'<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, lat_lon)))+'&deg;<br>'+
    		'<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, lat_lon)/1000)*10)/10+'km<br>'+
    		'<a href="javascript:elevation_profile('+elevation_vars+')"><b>Path Elevation Profile</b></a>';
    }
    infoTab += '</div>';
    var descTab = '<div class="user_bubble_desc">'+
        marker.station_desc;
    if(marker.station_website!='') {
    	descTab += '<br><br><a href="'+marker.station_website+'" target="_blank"><b>'+marker.station_website+'</b></a>';
    }
    descTab += '</div>';
    
    var infoBubble = new InfoBubble({
        maxWidth: 150,
        minWidth: 150,
        maxHeight: 110,
        minHeight: 110,
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
  	
    infoBubble.addTab('<span class="bubble_label">Info</span>', infoTab);
    infoBubble.addTab('<span class="bubble_label">Description</span>', descTab);

    google.maps.event.addListener(marker, 'click', function() {
        if (!infoBubble.isOpen()) {
            infoBubble.open(map, marker);
        }
    });
}

function updateUserMarker(user_data, user_index) {
	if(user_data['seconds_active']>18) { // 18 seconds, should check in every 5 seconds
		user_markers[user_index].setOptions( {
			icon: userUnknownIcon, // white icon, if shown (spotted)
			zIndex: 11
		});
	} else if(user_data['radio_active']==1) {
		user_markers[user_index].setOptions( {
			icon: userActiveIcon, // green
			zIndex: 13
		});
	} else {
		user_markers[user_index].setOptions( {
			icon: userAwayIcon, // yellow
			zIndex: 12
		});
	}
    user_markers[user_index].activity = user_data['seconds_active'];
}

function createRepeaterMarker(repeater_data) {
	var latlon = new google.maps.LatLng(repeater_data['lat'], repeater_data['lon']);
	
	var marker = new google.maps.Marker({
        position: latlon,
        map: map,
        title: repeater_data['qrz']
	});
	
	if(typeof repeater_data['op'] != 'undefined') {
		marker.setOptions( {
			icon: repeaterIcon,
			zIndex: 9
		});
	} else {
		marker.setOptions( {
			icon: repeaterOfflineIcon,
			zIndex: 8
		});
	}
	
	marker.repeater_id = repeater_data['id'];
	marker.lat = repeater_data['lat'];
	marker.lon = repeater_data['lon'];
    marker.callsign = repeater_data['qrz'];
    if(typeof repeater_data['qth']!='undefined') marker.qth = repeater_data['qth'];
    marker.locator = repeater_data['loc'];
    
    if (typeof repeater_data['tx1'] != 'undefined') marker.tx1 = repeater_data['tx1'];
    if (typeof repeater_data['tx2'] != 'undefined') marker.tx2 = repeater_data['tx2'];
    if (typeof repeater_data['tx3'] != 'undefined') marker.tx3 = repeater_data['tx3'];
    if (typeof repeater_data['tx4'] != 'undefined') marker.tx4 = repeater_data['tx4'];
    if (typeof repeater_data['tx5'] != 'undefined') marker.tx5 = repeater_data['tx5'];
    if (typeof repeater_data['tx6'] != 'undefined') marker.tx6 = repeater_data['tx6'];
    if (typeof repeater_data['tx7'] != 'undefined') marker.tx7 = repeater_data['tx7'];
    if (typeof repeater_data['tx8'] != 'undefined') marker.tx8 = repeater_data['tx8'];
    if (typeof repeater_data['tx9'] != 'undefined') marker.tx9 = repeater_data['tx9'];
    
    if (typeof repeater_data['rx1'] != 'undefined') marker.rx1 = repeater_data['rx1'];
    if (typeof repeater_data['rx2'] != 'undefined') marker.rx2 = repeater_data['rx2'];
    if (typeof repeater_data['rx3'] != 'undefined') marker.rx3 = repeater_data['rx3'];
    if (typeof repeater_data['rx4'] != 'undefined') marker.rx4 = repeater_data['rx4'];
    if (typeof repeater_data['rx5'] != 'undefined') marker.rx5 = repeater_data['rx5'];
    if (typeof repeater_data['rx6'] != 'undefined') marker.rx6 = repeater_data['rx6'];
    if (typeof repeater_data['rx7'] != 'undefined') marker.rx7 = repeater_data['rx7'];
    if (typeof repeater_data['rx8'] != 'undefined') marker.rx8 = repeater_data['rx8'];
    if (typeof repeater_data['rx9'] != 'undefined') marker.rx9 = repeater_data['rx9'];
    
    marker.is2m = 0;
    marker.is70cm = 0;
    marker.is23cm = 0;
    marker.is13cm = 0;
    marker.is9cm = 0;
    marker.is6cm = 0;
    marker.is3cm = 0;
    if (typeof repeater_data['2m']!='undefined') marker.is2m = 1;
    if (typeof repeater_data['70cm']!='undefined') marker.is70cm = 1;
    if (typeof repeater_data['23cm']!='undefined') marker.is23cm = 1;
    if (typeof repeater_data['13cm']!='undefined') marker.is13cm = 1;
    if (typeof repeater_data['9cm']!='undefined') marker.is9cm = 1;
    if (typeof repeater_data['6cm']!='undefined') marker.is6cm = 1;
    if (typeof repeater_data['3cm']!='undefined') marker.is3cm = 1;
    
    marker.desc = repeater_data['desc']
    if (typeof repeater_data['www'] != 'undefined') {
    	marker.website = repeater_data['www']
    } else {
    	marker.website = '';
    }
    if (typeof repeater_data['keep'] != 'undefined') {
   		marker.keeper = repeater_data['keep']
    } else {
    	marker.keeper = '';
    }
    repeater_markers.push(marker);
    
    repeater_data = null;
    
    var infoTab = '<div class="repeater_bubble_info">'+
        '<h3 style="line-height: 0.3em;">'+marker.callsign+'</h3>'+
        '<b>'+marker.locator+'</b>';
    if(typeof marker.qth!='undefined') infoTab += '&nbsp;-&nbsp;'+marker.qth;
    if(logged_in) {
    	var user_latlng = new google.maps.LatLng(user_lat, user_lon);
    	var elevation_vars = "'"+user_callsign+"','"+user_lat+"','"+user_lon+"','"+marker.callsign+"','"+marker.lat+"','"+marker.lon+"'";
    	infoTab+='<br><br>'+
    		'<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, latlon)))+'&deg;<br>'+
    		'<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, latlon)/1000)*10)/10+'km<br>'+
    		'<a href="javascript:elevation_profile('+elevation_vars+')"><b>Path Elevation Profile</b></a>';
    }
    infoTab += '</div>';
    var freqTab = '<div class="repeater_bubble_freq">';
    
    if (typeof marker.tx1 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx1+'MHz</b><br>';
    if (typeof marker.tx2 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx2+'MHz</b><br>';
    if (typeof marker.tx3 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx3+'MHz</b><br>';
    if (typeof marker.tx4 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx4+'MHz</b><br>';
    if (typeof marker.tx5 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx5+'MHz</b><br>';
    if (typeof marker.tx6 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx6+'MHz</b><br>';
    if (typeof marker.tx7 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx7+'MHz</b><br>';
    if (typeof marker.tx8 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx8+'MHz</b><br>';
    if (typeof marker.tx9 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx9+'MHz</b><br>';
    
    if (typeof marker.rx1 != 'undefined') freqTab += '<br><b>RX:&nbsp;'+marker.rx1+'MHz</b><br>';
    if (typeof marker.rx2 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx2+'MHz</b><br>';
    if (typeof marker.rx3 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx3+'MHz</b><br>';
    if (typeof marker.rx4 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx4+'MHz</b><br>';
    if (typeof marker.rx5 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx5+'MHz</b><br>';
    if (typeof marker.rx6 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx6+'MHz</b><br>';
    if (typeof marker.rx7 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx7+'MHz</b><br>';
    if (typeof marker.rx8 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx8+'MHz</b><br>';
    if (typeof marker.rx9 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx9+'MHz</b><br>';
    
    freqTab += '</div>';
    
    var descTab = '<div class="repeater_bubble_desc">';
    descTab += marker.desc+'<br>';
    if (typeof marker.keeper != 'undefined') descTab += '<b>Keeper:</b>&nbsp;'+marker.keeper+'<br><br>';
    if (typeof marker.website != 'undefined') descTab += '<a href="'+marker.website+'" target="_blank"><b>Repeater Website</b></a>';
    descTab += '</div>';
    
    var infoBubble = new InfoBubble({
        maxWidth: 180,
        minWidth: 180,
        maxHeight: 110,
        minHeight: 110,
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
  	
    infoBubble.addTab('<span class="bubble_label">Info</span>', infoTab);
    infoBubble.addTab('<span class="bubble_label">Tx/Rx</span>', freqTab);
    infoBubble.addTab('<span class="bubble_label">Description</span>', descTab);

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
    	strokeOpacity: 1.0,
    	strokeWeight: 3,
    	geodesic: true,
        zIndex: 1
	});
	
	switch(spot_data['band_id']) {
		case 1: // 70cm
			spotLine.setOptions( {
				strokeColor: "#FF0000" //red
			});
			break
		case 2: // 23cm
			spotLine.setOptions( {
				strokeColor: "#FFA500", //orange
				strokeWeight: 5 // thicker line
			});
			break
		default: //13 cm and above
			spotLine.setOptions( {
				strokeColor: "#0404B4" //blue
			});
			break
	}
	
	switch(spot_data['mode_id']) {
		case 0: // Not defined - assume Digital
			spotLine.setOptions( {
				zIndex: 5
			});
			spotLine.mode = "Digital ATV";
			break;
		case 1: // Analog TV
			spotLine.setOptions( {
				zIndex: 4
			});
			spotLine.mode = "Analog ATV";
			break;
		case 2: // Digital TV (WB)
			spotLine.setOptions( {
				zIndex: 5
			});
			spotLine.mode = "Digital ATV";
			break;
		case 3: // Beacon
			var lineSymbol = {
				path: 'M 0,-0.5 0,0.5',
				strokeOpacity: 0.5,
				scale: 2
			};
			spotLine.setOptions( {
				strokeOpacity: 0,
				icons: [{
					icon: lineSymbol,
					offset: '0',
					repeat: '10px'
				}],
				zIndex: 3
			});
			spotLine.mode = "NB Beacon";
			break;
	}
	
	spotLine.spot_id = spot_data['id'];
	spotLine.band_id = spot_data['band_id'];
	spotLine.mode_id = spot_data['mode_id'];
	
	spotLine.primary_id = spot_data['primary_id'];
	spotLine.primary_callsign = primary_callsign;
	spotLine.secondary_id = spot_data['secondary_id'];
	spotLine.secondary_callsign = secondary_callsign;
	spotLine.secondary_isrepeater = spot_data['secondary_isrepeater']
	spotLine.time = spot_data['spot_time'];
	spotLine.ago = spot_data['seconds_ago'];
	spotLine.comments = spot_data['comments'];
	spotLine.date = parseInt(spot_data['spot_time'].substr(8,2))+"&nbsp;"+months[parseInt(spot_data['spot_time'].substr(5,2))]+"&nbsp;"+spot_data['spot_time'].substr(11,8);	
	spotLine.distance = Math.round((google.maps.geometry.spherical.computeDistanceBetween(primary_latlon, secondary_latlon)/1000)*10)/10;
	
	var infoContent = spotLine.date+"<br><b>"+primary_callsign+"</b>&nbsp;->&nbsp;"+"<b>"+secondary_callsign+"</b><br>"+bandFromID(spotLine.band_id)+"&nbsp;<i>"+spotLine.mode+"</i><br><i>"+spotLine.comments+"</i><br>"+spotLine.distance+"&nbsp;km";
	
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

function loadUsers(JSONinput) {
	var u_id = new Array();
	for(u_id in JSONinput){
		var user = JSONinput[u_id];
		if(user.length!=0) {
			createUserMarker(user);
		}
	}
}

function updateUsers(JSONinput) {
	var u_id = new Array();
	for(u_id in JSONinput){
		var user = JSONinput[u_id];
		if(user.length!=0) {
			var marker_search = $.grep(user_markers, function(e){ return e.user_id == user['id']; });
			if(marker_search.length==1) {
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
