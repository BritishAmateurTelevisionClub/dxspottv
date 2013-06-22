var valTimeSpan;
var valBandChoice = {};

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

function checkSpots() {
	for (var i=0; i<spot_lines.length; i++) {
		if(valBandChoice[spot_lines[i].band_id] && (spot_lines[i].ago<=valTimeSpan)) {
			spot_lines[i].setVisible(true);
		} else {
			spot_lines[i].setVisible(false);
		}
	}
}


function checkUsers() {
    for (var i=0; i<user_markers.length; i++) {
			if(user_markers[i].known=="1" && user_markers[i].activity<=60) { // Online (in last minute)
				user_markers[i].setVisible(true); // then show
			} else { // Are they part of a shown spot?
			    // Grep spot lines for user_id
			    var spot_search = $.grep(spot_lines, function(e){
				    return (e.primary_id == user_markers[i].user_id || e.secondary_id == user_markers[i].user_id);
			    });
			    var visibleToBe = false;
			    for (var j=0; j<spot_search.length; j++) {
			        if (spot_search[j].visible) {
			            visibleToBe = true;
			        }
			    }
			    if(visibleToBe) {
			        user_markers[i].setVisible(true);
			    } else {
			        user_markers[i].setVisible(false);
			    }
			}
	}
}

function checkRepeaters() {
	var repeater_select = $('#repeaterBox').is(":checked");
	var band_select = $('#band_select').val();
    for (var i=0; i<repeater_markers.length; i++) {
    		var visibleToBe = false;
			if(band_select=="all" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is70cm==1 && band_select=="70cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is23cm==1 && band_select=="23cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is13cm==1 && band_select=="13cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is9cm==1 && band_select=="9cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is6cm==1 && band_select=="6cm" && repeater_select) {
				visibleToBe = true;
			} else if(repeater_markers[i].is3cm==1 && band_select=="3cm" && repeater_select) {
				visibleToBe = true;
			} else { // Are they part of a shown spot?
			    // Grep spot lines for user_id
			    var spot_search = $.grep(spot_lines, function(e){
				    return ((e.secondary_isrepeater == '1') && e.secondary_id == repeater_markers[i].repeater_id);
			    });
			    for (var j=0; j<spot_search.length; j++) {
			        if (spot_search[j].visible) {
			            visibleToBe = true;
			        }
			    }
			}
			if(visibleToBe) {
		        repeater_markers[i].setVisible(true);
		    } else {
		        repeater_markers[i].setVisible(false);
		    }
	}
}
