// Set up map
//
var marker;
$(document).ready(function() {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=geometry&sensor=false&callback=initialize'; // callback: initialize()
	document.body.appendChild(script);
});

function placeMarker(location) {
  if ( marker ) {
    marker.setPosition(location);
  } else {
    marker = new google.maps.Marker({
      position: location,
      map: map
    });
  }
}

function initialize() {
	google.maps.visualRefresh = true;
	var mapOptions = {
		zoom: 6,
		center: new google.maps.LatLng(52.5, -1.25),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		streetViewControl: false
	};

	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

	google.maps.event.addListener(map, 'click', function(event) {
		$('#lat').val(event.latLng.lat());
		$('#lon').val(event.latLng.lng());
		placeMarker(event.latLng);
	});
}

// Set up button
//
$(document).ready(function() {
	$("#validationFailDialog").dialog({ autoOpen: false });
	$("#captchaFailDialog").dialog({ autoOpen: false });
	$('#register_form').validate({
		submitHandler: function(form) { }
	});
	$('#register_button').button().click( function() {
		if($("#register_form").valid()==true) {
			$.ajax({
				url: '/ajax/submitRegister.php',
				type: "GET",
				data: {
					fname: $('#fname').val(),
					callsign: $('#callsign').val(),
					passwd: $('#passwd').val(),
					email: $('#email').val(),
					locator: $('#locator').val(),
					lat: $('#lat').val(),
					lon: $('#lon').val(),
					recaptcha_challenge_field: $('[name="recaptcha_challenge_field"]').val(),
					recaptcha_response_field: $('[name="recaptcha_response_field"]').val()
				},
				success: function( data ) {
					console.log(data);
					var returnJSON = eval('(' + data + ')');
					if(returnJSON['successful']==1) {
						console.log("Registered!");
						$('#first_form').hide();
						$('#successMessage').show();
					} else {
						$('#first_form').show();
						Recaptcha.reload();
						if(returnJSON['error']==1) {
							$("#captchaFailDialog").dialog("open");
						} else {
							alert("An unknown error occurred, please try again.");
						}
					}
				}
			});
		}
	});
	$('#return_button').button().click( function() {
    	window.location.href = "/";
	});
});
