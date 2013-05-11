// Set up button
//
$(document).ready(function() {
	$("#validationFailDialog").dialog({ autoOpen: false });
	$('#register_form').validate();
	$('#register_button').button().click( function() {
		if($("#register_form").valid()==true) {
			$.ajax({
				url: '/ajax/submit_register.php',
				type: "GET",
				data: {
					fname: $('#fname').val(),
					callsign: $('#callsign').val(),
					passwd: $('#passwd').val(),
					email: $('#passwd').val(),
					locator: $('#locator').val(),
					lat: $('#lon').val(),
					lon: $('#lon').val(),
					recaptcha_challenge_field: $('[name="recaptcha_challenge_field"]').val(),
					recaptcha_response_field: $('[name="recaptcha_response_field"]').val()
				},
				success: function( data ) {
					console.log(data);
					var returnJSON = eval('(' + data + ')');
					if(returnJSON['successful']==1) {
						console.log("Registered!");
					} else {
						if(returnJSON['error']==1) {
							alert('Captcha Error');
						}
					}
				}
			});
		} else { // Form failed validation
			$("#validationFailDialog").dialog("open");
		}
	});
});