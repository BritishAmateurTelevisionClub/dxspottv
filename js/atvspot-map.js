var map;

$(document).ready(function() {
	initMap(52, 0, 8);
});

function initMap(centre_lat, centre_lon, zoom_level) {
    var latlng = new google.maps.LatLng(centre_lat, centre_lon);
    var myOptions = {
		zoom: zoom_level,
		scaleControl: true,
		scaleControlOptions: { position: google.maps.ControlPosition.BOTTOM_LEFT } ,
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		center: latlng,
		streetViewControl: false
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}
