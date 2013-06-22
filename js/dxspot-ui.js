var valTimeSpan;
var valBandChoice = {};

var cursorLocTimer;

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
	$('#search-loc-button').button().click( function() {
		randomLoc = $('#search-locator').val();
		latlon = LoctoLatLon(randomLoc);
		click_latlng = new google.maps.LatLng(latlon[0], latlon[1]);
		infoContent="<h3 style='line-height: 0.3em;'>"+randomLoc+"</h3>";
		if(logged_in) {
			var user_latlng = new google.maps.LatLng(user_lat, user_lon);
			var elevation_vars = "'"+user_callsign+"','"+user_lat+"','"+user_lon+"','"+randomLoc+"','"+event.latLng.lat()+"','"+event.latLng.lng()+"'";
			infoContent+='<br>'+
			'<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, event.latLng)))+'&deg;<br>'+
			'<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, event.latLng)/1000)*10)/10+'km<br>'+
			'<a href="javascript:elevation_profile('+elevation_vars+')"><b>Path Elevation Profile</b></a>';
		}
		infowindow.setContent(infoContent);
		infowindow.setPosition(click_latlng);
		infowindow.open(map);
	});
	$('#search-call-button').button().click( function() {
		alert("Not yet implemented");
	});
	$('#loginWindow-button').button().click( function() {
		$("#box-info").hide("slide", { direction: "up" }, 500);
		$("#box-selectors").hide("slide", { direction: "up" }, 500);
    	$("#box-log").hide("slide", { direction: "right" }, 500);
    	$("#map-canvas").fadeTo(500, 0.2);
    	$("#window-login").show();
	});
	$('#login-login-button').button().click( function() {
		alert("Not yet implemented");
	});
	$('#login-cancel-button').button().click( function() {
		$("#box-info").show("slide", { direction: "up" }, 250);
		$("#box-selectors").show("slide", { direction: "up" }, 250);
    	$("#box-log").show("slide", { direction: "right" }, 250);
    	$("#map-canvas").fadeTo(500, 1);
    	$("#window-login").fadeOut(200);
	});
	// Make UI elements such as windows draggable
    $("#box-info").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
    $("#box-log").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
    $("#box-selectors").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
    $("#box-search").draggable({containment: '#map-canvas', handle: 'img.handle', snap: true});
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

function createGlobalSpotLog(spotLog) {
	var spotLogDivContent = "";
	if(spotLog.length!=0) {
	    var spot = new Array();
	    for(s_id in spotLog){
	    	if(s_id=="last") break;
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
		    spotLogDivContent+=parseInt(spot['spot_time'].substr(8,2),10)+"&nbsp;"+months[parseInt(spot['spot_time'].substr(5,2))]+"&nbsp;"+spot['spot_time'].substr(11,8)+":&nbsp;<b>"+primary_search[0].callsign+"</b>-><b>"+secondary_search[0].callsign+"</b>";
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

function showMousePos(GLatLng) {
	clearTimeout(cursorLocTimer);
    cursorLocTimer=setTimeout(function(){
    	var curr_lat = GLatLng.lat().toFixed(4);
		var curr_lon = GLatLng.lng().toFixed(4);
		$("#cursor_lat").html(curr_lat);
		$("#cursor_lon").html(curr_lon);
		$("#cursor_loc").html(CoordToLoc(parseFloat(curr_lat), parseFloat(curr_lon)));
    },10); // 10ms timeout for mouse to stay still before calculating
}
