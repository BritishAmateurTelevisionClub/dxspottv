
loadUsers(initData['users']);
parseRepeaters(initData['repeaters']);
parseSpots(initData['spots']);
initData = null;
checkSpots();
checkUsers();
checkRepeaters();

function createUserMarker(user_data) {    

	if(user_data['seconds_active']>18) { // 18 seconds, should check in every 5 seconds
	    user_markers[user_data['id']] = L.marker([user_data['lat'],user_data['lon']], {
            title: user_data['callsign'],
            icon: userUnknownIcon,
            zIndexOffset: 11
        }).addTo(map);
	} else if(user_data['id']==1) {
	    user_markers[user_data['id']] = L.marker([user_data['lat'],user_data['lon']], {
            title: user_data['callsign'],
            icon: userActiveIcon,
            zIndexOffset: 13
        }).addTo(map);
	} else {
	    user_markers[user_data['id']] = L.marker([user_data['lat'],user_data['lon']], {
            title: user_data['callsign'],
            icon: userAwayIcon,
            zIndexOffset: 12
        }).addTo(map);
	}
	
	var infoHTML = '<h3 style="line-height: 0.3em;">'+user_data['callsign']+'</h3>'+
                    '<b>'+user_data['locator']+'</b>';
    if(user_data['website']!='') {
    	infoHTML += '<br><br><a href="'+"http://"+user_data['website']+'" target="_blank"><b>'+"http://"+user_data['website']+'</b></a>';
    }
    
	user_markers[user_data['id']].bindPopup(infoHTML);
}

/*
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
*/

function createRepeaterMarker(repeater_data) {
	
	if(typeof repeater_data['op'] != 'undefined') {
		repeater_markers[repeater_data['qrz']] = L.marker([repeater_data['lat'],repeater_data['lon']], {
            title: repeater_data['qrz'],
            icon: repeaterIcon,
            zIndexOffset: 9
        }).addTo(map);
	} else {
		repeater_markers[repeater_data['qrz']] = L.marker([repeater_data['lat'],repeater_data['lon']], {
            title: repeater_data['qrz'],
            icon: repeaterOfflineIcon,
            zIndexOffset: 8
        }).addTo(map);
	}
	
	var infoHTML = '<h3 style="line-height: 0.3em;">'+repeater_data['qrz']+'</h3>'+
                    '<b>'+repeater_data['loc']+'</b>';
    if(repeater_data['www']!='') {
    	infoHTML += '<br><br><a href="'+"http://"+repeater_data['www']+'" target="_blank"><b>'+"http://"+repeater_data['www']+'</b></a>';
    }
    
	repeater_markers[repeater_data['qrz']].bindPopup(infoHTML);
    /*
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
    */
    repeater_markers[repeater_data['qrz']].is2m = 0;
    repeater_markers[repeater_data['qrz']].is70cm = 0;
    repeater_markers[repeater_data['qrz']].is23cm = 0;
    repeater_markers[repeater_data['qrz']].is13cm = 0;
    repeater_markers[repeater_data['qrz']].is9cm = 0;
    repeater_markers[repeater_data['qrz']].is6cm = 0;
    repeater_markers[repeater_data['qrz']].is3cm = 0;
    if (typeof repeater_data['2m']!='undefined') repeater_markers[repeater_data['qrz']].is2m = 1;
    if (typeof repeater_data['70cm']!='undefined') repeater_markers[repeater_data['qrz']].is70cm = 1;
    if (typeof repeater_data['23cm']!='undefined') repeater_markers[repeater_data['qrz']].is23cm = 1;
    if (typeof repeater_data['13cm']!='undefined') repeater_markers[repeater_data['qrz']].is13cm = 1;
    if (typeof repeater_data['9cm']!='undefined') repeater_markers[repeater_data['qrz']].is9cm = 1;
    if (typeof repeater_data['6cm']!='undefined') repeater_markers[repeater_data['qrz']].is6cm = 1;
    if (typeof repeater_data['3cm']!='undefined') repeater_markers[repeater_data['qrz']].is3cm = 1;
}

function createSpotLine(spot_data) {
	if(spot_data['secondary_isrepeater']==0) {
		var secondary_latlon = user_markers[spot_data['secondary_id']].getLatLng();
		var secondary_callsign = user_markers[spot_data['secondary_id']].title;
	} else {
		var secondary_latlon = repeater_markers[spot_data['secondary_id']].getLatLng();
		var secondary_callsign = repeater_markers[spot_data['secondary_id']].title;
	}
	
	var spotLineCoordinates = [
		user_markers[spot_data['primary_id']].getLatLng(),
		secondary_latlon
	];
	
	spot_lines[spot_data['id']] = L.polyline(spotLineCoordinates, {color: 'red'}).addTo(map);
	
	switch(spot_data['band_id']) {
		case 1: // 70cm
			spot_lines[spot_data['id']] = L.polyline(spotLineCoordinates, {color: '#FF0000'}).addTo(map);
			break
		case 2: // 23cm
			spot_lines[spot_data['id']] = L.polyline(spotLineCoordinates, {color: '#FFA500'}).addTo(map);
			break
		default: //13 cm and above
			spot_lines[spot_data['id']] = L.polyline(spotLineCoordinates, {color: '#0404B4'}).addTo(map);
			break
	}
	
	if(spot_data['mode_id']==3) { // Beacon
		spot_lines[spot_data['id']].setStyle({dashArray:"5, 1"});
	}
	
	spotLine.date = parseInt(spot_data['spot_time'].substr(8,2))+"&nbsp;"+months[parseInt(spot_data['spot_time'].substr(5,2))]+"&nbsp;"+spot_data['spot_time'].substr(11,8);	
	spotLine.distance = Math.round((google.maps.geometry.spherical.computeDistanceBetween(primary_latlon, secondary_latlon)/1000)*10)/10;
	
	var infoContent = spotLine.date+"<br><b>"+primary_callsign+"</b>&nbsp;->&nbsp;"+"<b>"+secondary_callsign+"</b><br>"+bandFromID(spotLine.band_id)+"&nbsp;<i>"+spotLine.mode+"</i><br><i>"+spotLine.comments+"</i><br>"+spotLine.distance+"&nbsp;km";
	
	google.maps.event.addListener(spotLine, 'click', function() {
		infowindow.setContent(infoContent);
		infowindow.setPosition(new google.maps.LatLng((primary_latlon.lat() + secondary_latlon.lat())/2, (primary_latlon.lng() + secondary_latlon.lng())/2));
    	infowindow.open(map);
   	});
	
	var infoHTML = '<h3 style="line-height: 0.3em;">'+user_markers[spot_data['primary_id']].title+"</b>&nbsp;->&nbsp;<b>"+secondary_callsign+'</h3>';
    
	spot_lines[spot_data['id']].bindPopup(infoHTML);
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

/*
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
*/

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
