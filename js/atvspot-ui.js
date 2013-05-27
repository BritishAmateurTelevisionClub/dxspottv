var months = ["_dummy_", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
// Set up Time/Band dropdowns
//
var valTimeSpan;
//var bandDict = {70cm: 1, 23cm: 2, 13cm: 3, 9cm: 5, 6cm: 6, 3cm: 4};
var valBandChoice = {}; // Gets setup on .ready()
var spotAutocomplete = [];
$(document).ready(function() {
	$('#time_select').change(function() {
		setTimeSpan($('#time_select').val());
		checkSpots();
		checkUsers();
		checkRepeaters();
	});
	$('#band_select').change(function() {
	    setBandChoice($('#band_select').val());
	    checkSpots();
		checkUsers();
		checkRepeaters();
	});
    setTimeSpan($('#time_select').val());
	setBandChoice($('#band_select').val());
	checkSpots();
	checkUsers();
	checkRepeaters();
});

// Spot Form
var validSpotForm;
$(document).ready(function() {
    $('#remote_callsign').focusout(function() {
        var remoteCallsign = $('#remote_callsign').val();
    	if($.inArray(remoteCallsign,spotAutocomplete)>=0) {
    	    var locator_search = $.grep(user_markers, function(e){ return e.callsign == remoteCallsign; });
    	    $('#remote_loc').val(locator_search[0].locator);
    	}
	});
	$('#spot_button').button().click( function() {
		if($('#remote_callsign').val().length>=4) {
    		submitSpot();
    	} else {
    		$('#submitStatus').show();
    		$('#submitStatus').html("<font color=red>Need callsign.</font>");
    	}
	});
});
// Set up Users/Repeaters checkboxes
//
$(document).ready(function() {
	// Both checkboxes checked
	$('#repeaterBox').prop('checked', true);
	// So show all to start! (done in map load callback)
	// Then functions for if changed
	$('#repeaterBox').change(function() {
	    infowindow.close();
		checkRepeaters();
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

// Station Description Edit Function
$(document).ready(function() {
	$('#station_description_edit').val(user_desc);
	$('#desc_button').button().click( function() {
		doChangeDesc($('#station_description_edit').val());
	});
});

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
		    spotLogDivContent+="&nbsp;"+bandFromID(spot.band_id);
		    if(spot['comments'].length != 0) {
			    spotLogDivContent+="<br>";
			    spotLogDivContent+="<i>"+spot['comments']+"</i>";
		    }
		    spotLogDivContent+="<br><br>";
	    }
	} else {
	    spotLogDivContent="No spots found.";
	}
	$('#spotLog').html(spotLogDivContent);
}

function loadSpotAutocomplete() {
    var callsigns = new Array();
    for (var i=0; i<user_markers.length; i++) {
        callsigns.push(user_markers[i].callsign);
    }
    for (var i=0; i<repeater_markers.length; i++) {
        callsigns.push(repeater_markers[i].callsign);
    }
    spotAutocomplete = callsigns;
    $("#remote_callsign").autocomplete({
      source: spotAutocomplete
    });
}
