// Set up Time/Band dropdowns
//
$(document).ready(function() {
	$('#time_select').change(function() {
		var nuTimeSpan = timespan_select.value;
	});
	$('#band_select').change(function() {
		changeUsersBandSelect($('#band_select').val());
		changeRepeatersBandSelect($('#band_select').val());
	});
});

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
$(window).load(function () {
	document.getElementById('irc_frame').src = irc_frame_source;
});

// Set up login/logout buttons
//
$(document).ready(function() {
	// submit form if enter is pressed
	$('#callsign_input').keypress(function(e) {
            if(e.which == 10 || e.which == 13) {
                doLogin();
            }
    });
    $('#passwd_input').keypress(function(e) {
            if(e.which == 10 || e.which == 13) {
                doLogin();
            }
    });
	$('#login_button').button().click( function() {
    	doLogin();
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
		updateListening();
	});
	$('#listen_23cm_box').change(function() {
		if ($('#listen_23cm_box').is(":checked")) {
			$('#listen_23cm_options').show(50);
		} else {
			$('#listen_23cm_options').hide(50);
		}
		updateListening();
	});
	$('#listen_13cm_box').change(function() {
		if ($('#listen_13cm_box').is(":checked")) {
			$('#listen_13cm_options').show(50);
		} else {
			$('#listen_13cm_options').hide(50);
		}
		updateListening();
	});
});

function createGlobalSpotLog(spotLog) {
	var spotLogDivContent = "";
	var spot = new Array();
	for(s_id in spotLog){
		var spot = spotLog[s_id];
		var primary_search = $.grep(user_markers, function(e){
			return e.user_id == spot.primary_id;
		});
		// find our secondary marker
		if(spot.secondary_isrepeater==1) { // if its a repeater
			var secondary_search = $.grep(repeater_markers, function(e){
				return e.repeater_id == spot.secondary_id;
			});
		} else { // or a user
			var secondary_search = $.grep(user_markers, function(e){
				return e.user_id == spot.secondary_id;
			});
		}
		spotLogDivContent+=spot['time'].substr(11,8);
		spotLogDivContent+=":&nbsp<b>"+primary_search[0].callsign+"</b>-><b>"+secondary_search[0].callsign+"</b>";
		spotLogDivContent+="<br>";
		spotLogDivContent+="Frequency:&nbsp;"+spot['frequency']+"MHz";
		spotLogDivContent+="<br><br>";
	}
	$('#spotLog').html(spotLogDivContent);
}
