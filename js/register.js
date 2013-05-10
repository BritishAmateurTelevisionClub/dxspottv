// Set up button
//
$(document).ready(function() {
	$('#register_form').validate();
	$('#register_button').button().click( function() {
		$.ajax({
			url: '/ajax/submit_register.php',
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
			}
		});
	});
});
