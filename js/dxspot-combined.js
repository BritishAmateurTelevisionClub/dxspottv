var map;

google.maps.visualRefresh = true;

function initialize() {
  var mapOptions = {
    zoom: 6,
    center: new google.maps.LatLng(52.5, -1.25),
    mapTypeId: google.maps.MapTypeId.TERRAIN,
	streetViewControl: false,
	mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
      position: google.maps.ControlPosition.TOP_LEFT
    }
  };
  
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  
  if (typeof user_lat != 'undefined') {
	map.setOptions({ center: new google.maps.LatLng(user_lat, user_lon) });
	}

	infowindow = new google.maps.InfoWindow( {
			size: new google.maps.Size(150,50)
	});

	elevator = new google.maps.ElevationService();
	
	google.maps.event.addListener(map, 'mousemove', function(event) {
        showMousePos(event.latLng);
    });

	google.maps.event.addListener(map, 'click', function() {
		infowindow.close();
	});

	google.maps.event.addListener(map, "rightclick", function(event) {
		var randomLoc = CoordToLoc(event.latLng.lat(), event.latLng.lng());
		infoContent="<h3 style='line-height: 0.3em;'>"+randomLoc+"</h3>";
		if(logged_in) {
			var user_latlng = new google.maps.LatLng(user_lat, user_lon);
			var elevation_vars = "'"+user_callsign+"','"+user_lat+"','"+user_lon+"','"+randomLoc+"','"+event.latLng.lat()+"','"+event.latLng.lng()+"'";
			infoContent+='<br>'+
			'<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, event.latLng)))+'&deg;<br>'+
			'<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, event.latLng)/1000)*10)/10+'km<br>'+
			'<a href="javascript:elevation_profile('+elevation_vars+')"><b>Path Elevation Profile</b></a>';
		}
		infowindow.setContent(infoContent);
		infowindow.setPosition(event.latLng);
		infowindow.open(map);
	});
	userActiveIcon = new google.maps.MarkerImage("/images/active_user.ico");
	userAwayIcon = new google.maps.MarkerImage("/images/away_user.ico");
	userUnknownIcon = new google.maps.MarkerImage("/images/unknown_user.ico");
	repeaterIcon = new google.maps.MarkerImage("/images/active_repeater.ico");
	repeaterOfflineIcon = new google.maps.MarkerImage("/images/inactive_repeater.ico");
	
	initialLoad();
}

google.maps.event.addDomListener(window, 'load', initialize);
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
var valTimeSpan;
var valBandChoice = {};

var cursorLocTimer;

$(document).ready(function() {
	$('#time_select').change(function() {
		setTimeSpan($('#time_select').val());
		checkSpots();
		checkUsers();
		checkRepeaters();
	});
	$('#band_select').change(function() {
	    setBandChoice($('#band_select').val());
	    checkSpots();
		checkUsers();
		checkRepeaters();
	});
	setTimeSpan($('#time_select').val());
	setBandChoice($('#band_select').val());
	$('#search-loc-button').button().click( function() {
		searchLocator();
	});
	$('#search-locator').keypress(function(e) {
		if(e.which == 10 || e.which == 13) {
		    searchLocator();
		}
    });
	$('#search-call-button').button().click( function() {
		alert("Not yet implemented");
	});
	$('#spot-button').button().click( function() {
		alert("Not yet implemented");
	});
	$('#loginWindow-button').button().click( function() {
		fadeToBlack();
    	$("#window-login").show();
	});
	$('#login-login-button').button().click( function() {
		doLogin();
	});
	$('#callsign-input').keypress(function(e) {
		if(e.which == 10 || e.which == 13) {
    		doLogin();
    	}
    });
    $('#passwd-input').keypress(function(e) {
		if(e.which == 10 || e.which == 13) {
    		doLogin();
    	}
    });
	$('#login-cancel-button').button().click( function() {
		fadeToUI();
    	$("#window-login").fadeOut(200);
	});
	$('#aboutWindow-button').button().click( function() {
		fadeToBlack();
    	$("#window-about").show();
	});
	$('#about-back-button').button().click( function() {
		fadeToUI();
    	$("#window-about").fadeOut(200);
	});
	$('#helpWindow-button').button().click( function() {
		fadeToBlack();
    	$("#window-help").show();
	});
	$('#help-back-button').button().click( function() {
		fadeToUI();
    	$("#window-help").fadeOut(200);
	});
	// Make UI elements such as windows draggable
    $("#box-info").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
    $("#box-log").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
    $("#box-selectors").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
    $("#box-search").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
    $("#box-spot").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
});

function fadeToBlack() {
	$("#box-info").hide();
	$("#box-selectors").hide();
	$("#box-log").hide();
	$("#box-search").hide();
	$("#box-spot").hide();
	$("#box-multi").hide();
	$("#map-canvas").fadeTo(500, 0.2);
}

function fadeToUI() {
	$("#box-info").show();
	$("#box-selectors").show();
	$("#box-log").show();
	$("#box-search").show();
	$("#box-spot").show();
	$("#box-multi").show();
	$("#map-canvas").fadeTo(500, 1);
}

function doLogin() {
	dataSocket.emit('login', { callsign: $("#callsign-input").val(), password: $("#passwd-input").val() });
}

function searchLocator() {
	randomLoc = $('#search-locator').val();
	latlon = LoctoLatLon(randomLoc);
	click_latlng = new google.maps.LatLng(latlon[0], latlon[1]);
	infoContent="<h3 style='line-height: 0.3em;'>"+randomLoc+"</h3>";
	if(logged_in) {
		var user_latlng = new google.maps.LatLng(user_lat, user_lon);
		var elevation_vars = "'"+user_callsign+"','"+user_lat+"','"+user_lon+"','"+randomLoc+"','"+event.latLng.lat()+"','"+event.latLng.lng()+"'";
		infoContent+='<br>'+
		'<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, event.latLng)))+'&deg;<br>'+
		'<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, event.latLng)/1000)*10)/10+'km<br>'+
		'<a href="javascript:elevation_profile('+elevation_vars+')"><b>Path Elevation Profile</b></a>';
	}
	infowindow.setContent(infoContent);
	infowindow.setPosition(click_latlng);
	infowindow.open(map);
}

// Set up Users/Repeaters checkboxes
//
$(document).ready(function() {
	// Both checkboxes checked
	$('#repeaterBox').prop('checked', true);
	// So show all to start! (done in map load callback)
	// Then functions for if changed
	$('#repeaterBox').change(function() {
	    infowindow.close();
		checkRepeaters();
	});
});

function checkSpots() {
	for (var i=0; i<spot_lines.length; i++) {
		if(valBandChoice[spot_lines[i].band_id] && (spot_lines[i].ago<=valTimeSpan)) {
			spot_lines[i].setVisible(true);
		} else {
			spot_lines[i].setVisible(false);
		}
	}
}


function checkUsers() {
    for (var i=0; i<user_markers.length; i++) {
			if(user_markers[i].known=="1" && user_markers[i].activity<=60) { // Online (in last minute)
				user_markers[i].setVisible(true); // then show
			} else { // Are they part of a shown spot?
			    // Grep spot lines for user_id
			    var spot_search = $.grep(spot_lines, function(e){
				    return (e.primary_id == user_markers[i].user_id || e.secondary_id == user_markers[i].user_id);
			    });
			    var visibleToBe = false;
			    for (var j=0; j<spot_search.length; j++) {
			        if (spot_search[j].visible) {
			            visibleToBe = true;
			        }
			    }
			    if(visibleToBe) {
			        user_markers[i].setVisible(true);
			    } else {
			        user_markers[i].setVisible(false);
			    }
			}
	}
}

function checkRepeaters() {
	var repeater_select = $('#repeaterBox').is(":checked");
	var band_select = $('#band_select').val();
    for (var i=0; i<repeater_markers.length; i++) {
    		var visibleToBe = false;
			if(band_select=="all" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is70cm==1 && band_select=="70cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is23cm==1 && band_select=="23cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is13cm==1 && band_select=="13cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is9cm==1 && band_select=="9cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is6cm==1 && band_select=="6cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is3cm==1 && band_select=="3cm" && repeater_select) {
				visibleToBe = true;
			} else { // Are they part of a shown spot?
			    // Grep spot lines for user_id
			    var spot_search = $.grep(spot_lines, function(e){
				    return ((e.secondary_isrepeater == '1') && e.secondary_id == repeater_markers[i].repeater_id);
			    });
			    for (var j=0; j<spot_search.length; j++) {
			        if (spot_search[j].visible) {
			            visibleToBe = true;
			        }
			    }
			}
			if(visibleToBe) {
		        repeater_markers[i].setVisible(true);
		    } else {
		        repeater_markers[i].setVisible(false);
		    }
	}
}

function createGlobalSpotLog(spotLog) {
	var spotLogDivContent = "";
	if(spotLog.length!=0) {
	    var spot = new Array();
	    for(s_id in spotLog){
	        if(spotLog[s_id].seconds_ago>604800) break;
	    	if(s_id=="last") break;
		    var spot = spotLog[s_id];
		    var primary_search = $.grep(user_markers, function(e){
			    return e.user_id == spot.primary_id;
		    });
		    // find our secondary marker
		    if(spot.secondary_isrepeater==1) { // if its a repeater
			    var secondary_search = $.grep(repeater_markers, function(e){
				    return e.repeater_id == spot.secondary_id;
			    });
		    } else { // or a user
			    var secondary_search = $.grep(user_markers, function(e){
				    return e.user_id == spot.secondary_id;
			    });
		    }
		    spotLogDivContent+=parseInt(spot['spot_time'].substr(8,2),10)+"&nbsp;"+months[parseInt(spot['spot_time'].substr(5,2))]+"&nbsp;"+spot['spot_time'].substr(11,8)+":&nbsp;<b>"+primary_search[0].callsign+"</b>-><b>"+secondary_search[0].callsign+"</b>";
		    spotLogDivContent+="&nbsp;"+bandFromID(spot.band_id);
		    if(spot['comments'].length != 0) {
			    spotLogDivContent+="<br>";
			    spotLogDivContent+="<i>"+spot['comments']+"</i>";
		    }
		    spotLogDivContent+="<br><br>";
	    }
	} else {
	    spotLogDivContent="No spots found.";
	}
	$('#spotLog').html(spotLogDivContent);
}

function showMousePos(GLatLng) {
	clearTimeout(cursorLocTimer);
    cursorLocTimer=setTimeout(function(){
    	var curr_lat = GLatLng.lat().toFixed(4);
		var curr_lon = GLatLng.lng().toFixed(4);
		$("#cursor_lat").html(curr_lat);
		$("#cursor_lon").html(curr_lon);
		$("#cursor_loc").html(CoordToLoc(parseFloat(curr_lat), parseFloat(curr_lon)));
    },10); // 10ms timeout for mouse to stay still before calculating
}
if(!Array.prototype.last) {
	Array.prototype.last = function() {
		return this[this.length - 1];
	}
}

function setTimeSpan(timeSpan) {
	switch(timeSpan)
	{
	case "year":
		valTimeSpan = 31557600;
		break;
	case "6months":
		valTimeSpan = 15778800;
		break;
	case "1month":
		valTimeSpan = 2678400;
		break;
	case "1week":
		valTimeSpan = 604800;
		break;
	case "24hours":
		valTimeSpan = 86400;
		break;
	case "12hours":
		valTimeSpan = 43200;
		break;
	case "6hours":
		valTimeSpan = 21600;
		break;
	default: // All
		valTimeSpan = 315576000; // 10 years, should do for now!
		break;
	}
}

function setBandChoice(bandChoice) {
	switch(bandChoice)
	{
	case "70cm":
		valBandChoice = { 1: true, 2: false, 3: false, 4: false, 5: false, 6:false};
		break;
	case "23cm":
		valBandChoice = { 1: false, 2: true, 3: false, 4: false, 5: false, 6:false};
		break;
	case "13cm":
		valBandChoice = { 1: false, 2: false, 3: true, 4: false, 5: false, 6:false};
		break;
	case "9cm":
		valBandChoice = { 1: false, 2: false, 3: false, 4: false, 5: true, 6:false};
		break;
	case "6cm":
		valBandChoice = { 1: false, 2: false, 3: false, 4: false, 5: false, 6:true};
		break;
	case "3cm":
		valBandChoice = { 1: false, 2: false, 3: false, 4: true, 5: false, 6:false};
		break;
	default: // All
		valBandChoice = { 1: true, 2: true, 3: true, 4: true, 5: true, 6:true};
		break;
	}
}

function bandFromID(bandID) {
    switch(bandID)
	{
	case 1:
		return "70cm";
		break;
	case 2:
		return "23cm";
		break;
	case 3:
		return "13cm";
		break;
	case 4:
		return "3cm";
		break;
	case 5:
		return "9cm";
		break;
	case 6:
		return "6cm";
		break;
	default:
	    return "ERROR";
		break;
	}
}

function convertHeading(input) {
	if (input >=0) {
		return input;
	} else {
		return 360+input;
	}
}
// Source: http://dauda.at/locator/locator.js
// Credit: (c) 2009 Mike, OE3MDC

function getDeg(arg, base, offset, cmp)
// convert letters into angles by subtracting the base char code from the input char code
{
    return(base + offset * (arg.toUpperCase().charCodeAt(0) - cmp.charCodeAt(0)));
}

function LoctoLatLon(maidenhead) {
      var x = 0;
		var sw_lon;
		var ne_lon;
		var ce_lon;
		var sw_lat;
		var ne_lat;
		var ce_lat;
        while (x < maidenhead.length)
        {
            switch(x)
            {
                case 0:
                    sw_lon = getDeg(maidenhead.charAt(x), -180, 20, "A");
                    ne_lon = sw_lon + 20;
                    ce_lon = sw_lon + 10;
                    break;
                case 1:
                    sw_lat = getDeg(maidenhead.charAt(x), -90, 10, "A");
                    ne_lat = sw_lat + 10;
                    ce_lat = sw_lat + 5;
                    break;
                case 2:
                    sw_lon += getDeg(maidenhead.charAt(x), 0, 2, "0");
                    ne_lon = sw_lon + 2;
                    ce_lon = sw_lon + 1;
                    break;
                case 3:
                    sw_lat += getDeg(maidenhead.charAt(x), 0, 1, "0");
                    ne_lat = sw_lat + 1;
                    ce_lat = sw_lat + 0.5;
                    break;
                case 4:
                    sw_lon += getDeg(maidenhead.charAt(x), 0, 2/24, "A");
                    ne_lon = sw_lon + 2/24;
                    ce_lon = sw_lon + 1/24;
                    break;
                case 5:
                    sw_lat += getDeg(maidenhead.charAt(x), 0, 1/24, "A");
                    ne_lat = sw_lat + 1/24;
                    ce_lat = sw_lat + 0.5/24;
                    break;
                default:
                    break;
            }
            x++;
        }
        return [ce_lat, ce_lon];
}

function CoordToLoc(Lat, Lon) {
    var Locator = "";

    Lon = Lon + 180; // we want positive values starting from 0
    Lat = Lat + 90;
    Lon = Lon / 20 + 0.0000001; // help for rounding
    Lat = Lat / 10 + 0.0000001;
    Locator = Locator + String.fromCharCode(65 + Lon) + String.fromCharCode(65 + Lat);
    Lon = Lon - Math.floor(Lon);
    Lat = Lat - Math.floor(Lat);
    Lon = Lon * 10;
    Lat = Lat * 10;
    Locator = Locator + String.fromCharCode(48 + Lon) + String.fromCharCode(48 + Lat);
    Lon = Lon - Math.floor(Lon);
    Lat = Lat - Math.floor(Lat);
    Lon = Lon * 24;
    Lat = Lat * 24;
    Locator = Locator + String.fromCharCode(65 + Lon) + String.fromCharCode(65 + Lat);
    return(Locator);
}
