var map;

google.maps.visualRefresh = true;

$(document).ready(function() {
	initUI();
});

function initUI() {
    // Make UI elements such as windows draggable
    $("#box-info").draggable({containment: '#map-canvas', handle: 'img.handle', snap: '#map-canvas'});
    // Activate buttons to jqueryui styling
}

function initialize() {
  var mapOptions = {
    zoom: 6,
    center: new google.maps.LatLng(52.5, -1.25),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	streetViewControl: false
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
}

google.maps.event.addDomListener(window, 'load', initialize);
