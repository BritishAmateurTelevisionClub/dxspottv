// Load IRC (using php-configured url)
//
$(document).ready(function() {
	document.getElementById('irc_frame').src = irc_frame_source;
});

// Show/Hide webIRC
//
function hideIRC() {
	$('#irc_frame').hide();
	$('#irc_shown_blurb').hide();
	$('#irc_hidden_blurb').show();
}
function showIRC() {
	$('#irc_hidden_blurb').hide();
	$('#irc_frame').show();
	$('#irc_shown_blurb').show();
}



// "I'm Listening" Frequency Boxes
//
$(document).ready(function() {
	$('#listen_70cm_box').change(function() {
		if ($('#listen_70cm_box').is(":checked")) {
			$('#listen_70cm_options').show(50);
		} else {
			$('#listen_70cm_options').hide(50);
		}
	});
	$('#listen_23cm_box').change(function() {
		if ($('#listen_23cm_box').is(":checked")) {
			$('#listen_23cm_options').show(50);
		} else {
			$('#listen_23cm_options').hide(50);
		}
	});
	$('#listen_13cm_box').change(function() {
		if ($('#listen_13cm_box').is(":checked")) {
			$('#listen_13cm_options').show(50);
		} else {
			$('#listen_13cm_options').hide(50);
		}
	});
});
