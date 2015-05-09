var map;

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
}


/* Creates a Repeater Marker for the map */
function newRepeaterMarker(repeater_data) {
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
