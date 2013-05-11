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
				url: '/ajax/submit_register.php',
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
		} else { // Form failed validation
			$("#validationFailDialog").dialog("open");
		}
	});
	$('#return_button').button().click( function() {
    	window.location.href = "/";
	});
});
