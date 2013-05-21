var months = ["_dummy_", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
// Set up Time/Band dropdowns
//
var valTimeSpan;
//var bandDict = {70cm: 1, 23cm: 2, 13cm: 3, 3cm: 4};
var valBandChoice = {}; // Gets setup on .ready()
$(document).ready(function() {
	setTimeSpan($('#time_select').val());
	setBandChoice($('#band_select').val());
	checkSpots();
	checkUsers();
	$('#time_select').change(function() {
		setTimeSpan($('#time_select').val());
		checkSpots();
		checkUsers();
	});
	$('#band_select').change(function() {
	    setBandChoice($('#band_select').val());
	    checkSpots();
		checkUsers();
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
	    infowindow.close();
		checkUsers();
	});
	$('#repeaterBox').change(function() {
	    infowindow.close();
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
		ga('send', 'event', 'action', 'Log In');
    	doLogin();
	});
	$('#logout_button').button().click( function() {
		ga('send', 'event', 'action', 'Log Out');
    	window.location.href = "/logout.php";
	});
	$('#register_button').button().click( function() {
		ga('send', 'event', 'action', 'Register');
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

function createGlobalSpotLog(spotLog) {
	var spotLogDivContent = "";
	if(spotLog.length!=0) {
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
		    spotLogDivContent+=parseInt(spot['time'].substr(8,2))+"&nbsp;"+months[parseInt(spot['time'].substr(5,2))]+"&nbsp;"+spot['time'].substr(11,8)+":&nbsp;<b>"+primary_search[0].callsign+"</b>-><b>"+secondary_search[0].callsign+"</b>";
		    spotLogDivContent+="&nbsp;"+spot['frequency']+"MHz";
		    if(spot['comments'].length != 0) {
			    spotLogDivContent+="<br>";
			    spotLogDivContent+="Comment:&nbsp;"+spot['comments'];
		    }
		    spotLogDivContent+="<br><br>";
	    }
	} else {
	    spotLogDivContent="No spots found.";
	}
	$('#spotLog').html(spotLogDivContent);
}
