var map;
var repeater_markers = new Array();

/* Load Map Marker Icons */
userActiveIcon = new google.maps.MarkerImage("/images/active_user.ico");
userAwayIcon = new google.maps.MarkerImage("/images/away_user.ico");
userUnknownIcon = new google.maps.MarkerImage("/images/unknown_user.ico");
repeaterIcon = new google.maps.MarkerImage("/images/active_repeater.ico");
repeaterOfflineIcon = new google.maps.MarkerImage("/images/inactive_repeater.ico");

/* Main Map Init Function */
function init_map()
{
  var mapCanvas = document.getElementById('map-canvas');
  var mapOptions = {
    center: new google.maps.LatLng(51, -1.4),
    zoom: 5,
    mapTypeId: google.maps.MapTypeId.TERRAIN
  }
  map = new google.maps.Map(mapCanvas, mapOptions);

  $.getJSON("/api/repeaters.php",function( json ) {
    if(!json.error)
    {
      var data_len = json.length;
      for(var i=0;i<data_len;i++)
      {
        newRepeaterMarker(json[i]);
      }
    }
    else
    {
      console.log(json);
    }
  });
  $.getJSON("/api/users.php",function( json ) {
    if(!json.error)
    {
      var data_len = json.length;
      for(var i=0;i<data_len;i++)
      {
        newUserMarker(json[i]);
      }
    }
    else
    {
      console.log(json);
    }
  });
}


/* Creates a Repeater Marker for the map */
function newRepeaterMarker(repeater_data) {
	var latlon = new google.maps.LatLng(repeater_data['lat'], repeater_data['lon']);
	
	var marker = new google.maps.Marker({
        position: latlon,
        map: map,
        title: repeater_data['callsign']
	});
	
	if(repeater_data['active'] == '1') {
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
    marker.callsign = repeater_data['callsign'];
    if(typeof repeater_data['qth']!='undefined') marker.qth = repeater_data['qth'];
    marker.locator = repeater_data['qth_r'];
    
    if (repeater_data['tx1'] != null && repeater_data['tx1'] != '0') marker.tx1 = repeater_data['tx1'];
    if (repeater_data['tx2'] != null && repeater_data['tx2'] != '0') marker.tx2 = repeater_data['tx2'];
    if (repeater_data['tx3'] != null && repeater_data['tx3'] != '0') marker.tx3 = repeater_data['tx3'];
    if (repeater_data['tx4'] != null && repeater_data['tx4'] != '0') marker.tx4 = repeater_data['tx4'];
    if (repeater_data['tx5'] != null && repeater_data['tx5'] != '0') marker.tx5 = repeater_data['tx5'];
    if (repeater_data['tx6'] != null && repeater_data['tx6'] != '0') marker.tx6 = repeater_data['tx6'];
    if (repeater_data['tx7'] != null && repeater_data['tx7'] != '0') marker.tx7 = repeater_data['tx7'];
    if (repeater_data['tx8'] != null && repeater_data['tx8'] != '0') marker.tx8 = repeater_data['tx8'];
    if (repeater_data['tx9'] != null && repeater_data['tx9'] != '0') marker.tx9 = repeater_data['tx9'];
    
    if (repeater_data['rx1'] != null && repeater_data['rx1'] != '0') marker.rx1 = repeater_data['rx1'];
    if (repeater_data['rx2'] != null && repeater_data['rx2'] != '0') marker.rx2 = repeater_data['rx2'];
    if (repeater_data['rx3'] != null && repeater_data['rx3'] != '0') marker.rx3 = repeater_data['rx3'];
    if (repeater_data['rx4'] != null && repeater_data['rx4'] != '0') marker.rx4 = repeater_data['rx4'];
    if (repeater_data['rx5'] != null && repeater_data['rx5'] != '0') marker.rx5 = repeater_data['rx5'];
    if (repeater_data['rx6'] != null && repeater_data['rx6'] != '0') marker.rx6 = repeater_data['rx6'];
    if (repeater_data['rx7'] != null && repeater_data['rx7'] != '0') marker.rx7 = repeater_data['rx7'];
    if (repeater_data['rx8'] != null && repeater_data['rx8'] != '0') marker.rx8 = repeater_data['rx8'];
    if (repeater_data['rx9'] != null && repeater_data['rx9'] != '0') marker.rx9 = repeater_data['rx9'];
    
    marker.is2m = 0;
    marker.is70cm = 0;
    marker.is23cm = 0;
    marker.is13cm = 0;
    marker.is9cm = 0;
    marker.is6cm = 0;
    marker.is3cm = 0;
    if (repeater_data['2m']!='0') marker.is2m = 1;
    if (repeater_data['70cm']!='0') marker.is70cm = 1;
    if (repeater_data['23cm']!='0') marker.is23cm = 1;
    if (repeater_data['13cm']!='0') marker.is13cm = 1;
    if (repeater_data['9cm']!='0') marker.is9cm = 1;
    if (repeater_data['6cm']!='0') marker.is6cm = 1;
    if (repeater_data['3cm']!='0') marker.is3cm = 1;
    
    marker.desc = repeater_data['description']
    if (repeater_data['website'] != null && repeater_data['website'] != "") {
    	marker.website = repeater_data['website']
    } else {
    	marker.website = '';
    }
    if (repeater_data['keeper_callsign'] != null && repeater_data['keeper_callsign'] != "") {
   		marker.keeper = repeater_data['keeper_callsign']
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
    	infoTab+='<br><br>'+
    		'<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, latlon)))+'&deg;<br>'+
    		'<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, latlon)/1000)*10)/10+'km<br>';
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
    if (marker.keeper != '') descTab += '<b>Keeper:</b>&nbsp;'+marker.keeper+'<br><br>';
    if (marker.website != '') descTab += '<a href="'+marker.website+'" target="_blank"><b>Repeater Website</b></a>';
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

function newUserMarker(user_data) {
	var lat_lon = new google.maps.LatLng(user_data['lat'], user_data['lon']);
	
	var marker = new google.maps.Marker({
        position: lat_lon,
        map: map,
        title: user_data['callsign']
    });
	
	if(user_data['activity_timer']>18) { // 18 seconds, should check in every 5 seconds
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
    marker.activity = user_data['activity_timer'];
    marker.known = user_data['known'];
    marker.station_desc = user_data['station_desc'];
    if(user_data['website']!='') {
    	marker.station_website = "https://"+user_data['website'];
    } else {
    	marker.station_website = '';
    }
    user_markers.push(marker);
    
    var infoTab = '<div class="user_bubble_info">'+
        '<h3 style="line-height: 0.3em;">'+marker.callsign+'</h3>'+
        '<b>'+marker.locator+'</b>';
    if(logged_in && (user_callsign!=user_data['callsign'])) {
    	var user_latlng = new google.maps.LatLng(user_lat, user_lon);
    	infoTab+='<br><br>'+
    		'<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, lat_lon)))+'&deg;<br>'+
    		'<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, lat_lon)/1000)*10)/10+'km<br>';
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
