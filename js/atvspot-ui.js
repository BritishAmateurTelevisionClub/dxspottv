// Set up Time/Band dropdowns
//
$(document).ready(function() {
	$('#time_select').change(function() {
		var nuTimeSpan = timespan_select.value;
	});
	$('#band_select').change(function() {
		changeRepeatersBandSelect($('#band_select').val());
	});
});

function changeRepeatersBandSelect(select_val) {
	switch(select_val)
	{
	case "70cm":
		for (var i=0; i<repeater_markers.length; i++) {
			if(repeater_markers[i].is70cm==1) {
				repeater_markers[i].setVisible(true);
			} else {
				repeater_markers[i].setVisible(false);
			}
		}
		break;
	case "23cm":
		for (var i=0; i<repeater_markers.length; i++) {
			if(repeater_markers[i].is23cm==1) {
				repeater_markers[i].setVisible(true);
			} else {
				repeater_markers[i].setVisible(false);
			}
		}
		break;
	case "13cm":
		for (var i=0; i<repeater_markers.length; i++) {
			if(repeater_markers[i].is13cm==1 || repeater_markers[i].is3cm==1) {
				repeater_markers[i].setVisible(true);
			} else {
				repeater_markers[i].setVisible(false);
			}
		}
		break;
	default: // All
		for (var i=0; i<repeater_markers.length; i++) {
			repeater_markers[i].setVisible(true);
		}
		break;
	}
}

// Spot Form
$(document).ready(function() {
	$('#spot_button').button().click( function() {
    	submitSpot();
	});
});
// Set up Users/Repeaters checkboxes
//
$(document).ready(function() {
	// Both checkboxes checked
	$('#userBox').prop('checked', true);
	$('#repeaterBox').prop('checked', true);
	// So show all to start! (done in map load callback)
	// Then functions for if changed
	$('#userBox').change(function() {
		if ($('#userBox').is(":checked")) {
			usersShow();
		} else {
			usersHide();
		}
	});
	$('#repeaterBox').change(function() {
		if ($('#repeaterBox').is(":checked")) {
			repeatersShow();
		} else {
			repeatersHide();
		}
	});
});

// Load IRC (using php-configured url)
//
$(document).ready(function() {
	document.getElementById('irc_frame').src = irc_frame_source;
});

// Set up login/logout buttons
//
$(document).ready(function() {
	$('#login_button').button().click( function() {
    	$('#login_form').submit();
	});
	$('#logout_button').button().click( function() {
    	window.location.href = "/logout.php";
	});
	$('#register_button').button().click( function() {
    	window.location.href = "/register.php";
	});
	
	// Set up tabs
	$( "#tabs" ).tabs();
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
