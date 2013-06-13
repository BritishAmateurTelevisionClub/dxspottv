var map;

$(document).ready(function() {
	initMap(52, 0, 8);
	
 	$("#scenario_info").draggable({containment: '#map_canvas', handle: 'img.handle', snap: '#map_canvas'});
});

function initMap(centre_lat, centre_lon, zoom_level) {
    var latlng = new google.maps.LatLng(centre_lat, centre_lon);
    google.maps.visualRefresh = true;
    var myOptions = {
		zoom: zoom_level,
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		center: latlng,
		streetViewControl: false
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}
