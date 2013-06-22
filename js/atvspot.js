var repeater_markers = [];
var user_markers = [];
var spot_lines = [];
var map;
var elevator;
var profile_path;

var infowindow;
var session_id;
var logged_in;

// Load Google Maps Script
//
$(document).ready(function() {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyCEzQiTsZ1Skkf7mS1QTT91C2Y_7Gi7WK0&libraries=geometry&sensor=false&callback=initialize'; // callback: initialize()
	document.body.appendChild(script);
});

google.load("visualization", "1", {packages:["corechart"]});

// Callback from Google Maps Script Load
//
function initialize() {
	google.maps.visualRefresh = true;
	var mapOptions = {
		zoom: 6,
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		streetViewControl: false
	};
	
	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
	
	if (typeof user_lat != 'undefined') {
		map.setOptions({ center: new google.maps.LatLng(user_lat, user_lon) });
	} else {
		map.setOptions({ center: new google.maps.LatLng(52.5, -1.25) });
	}

	infowindow = new google.maps.InfoWindow( {
			size: new google.maps.Size(150,50)
	});
	
	elevator = new google.maps.ElevationService();

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

	getMapData();
	userSpotRefresh=self.setInterval(function(){getUserSpotData()},2000+Math.round(Math.random()*200));
	repeaterRefresh=self.setInterval(function(){getRepeaterData()},120000+Math.round(Math.random()*2000));
}
