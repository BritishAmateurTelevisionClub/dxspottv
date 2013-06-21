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
    zoom: 8,
    center: new google.maps.LatLng(-34.397, 150.644),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	streetViewControl: false
  };
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

google.maps.event.addDomListener(window, 'load', initialize);
