// Set up refresh functions
//
var mapRefresh=self.setInterval(function(){getMapData()},2000+Math.round(Math.random()*200)); // Add from 0-200ms randomly

var activityRefresh=self.setInterval(function(){updateActivity()},5000+Math.round(Math.random()*400)); // Add from 0-400ms randomly

function doLogin() {
	$.ajax({
		url: "/login.php",
		type: "GET",
		data: {
			callsign: $("#callsign_input").val(),
			passwd: $('#passwd_input').val()
		},
		success: function( data ) {
			//console.log(data);
			location.reload(true);
		}
	});
}

function getMapData() {
	$.ajax({
		url: "/ajax/mapData.php",
		success: function( data ) {
			myJSONObject = eval('(' + data + ')');
    		parseUsers(myJSONObject['users']);
    		parseRepeaters(myJSONObject['repeaters']);
    		parseSpots(myJSONObject['spots']);
    		createGlobalSpotLog(myJSONObject['spots']);
    		
    		setTimeSpan($('#time_select').val());
			setBandChoice($('#band_select').val());
			checkSpots();
			checkUsers();
			checkRepeaters();
		
    		loadSpotAutocomplete();
		}
	});
	ga('send', 'event', 'refresh', 'Map Data');
}

function updateActivity() {
	$.ajax({
		url: "/ajax/update_activity.php",
		success: function( data ) {
			//console.log(data);
		}
	});
	ga('send', 'event', 'update', 'Activity Data');
}

function submitSpot() {
	var rlatlon = [];
	rlatlon = LoctoLatLon($("#remote_loc").val());
	ga('send', 'event', 'action', 'Submit Spot');
	$('#submitStatus').val("Submitting...");
	$('#submitStatus').show();
	$.ajax({
		url: "/ajax/submit_spot.php",
		type: "GET",
		data: {
			band_id: $("#spot_band_select").val(),
			mode: $("#spot_mode_select").val(),
			r_callsign: $("#remote_callsign").val(),
			r_locator: $("#remote_loc").val(),
			r_lat: rlatlon[0],
			r_lon: rlatlon[1],
			comments: $("#spot_comments").val()
		},
		success: function( data ) {
			//console.log(data);
			$('#submitStatus').html("<font color=green>Submitted.</font>"); // Clear status
			$('#submitStatus').hide(1200);
			// Now clear all the boxes
			$('#remote_callsign').val("");
			$('#remote_loc').val("");
			$('#spot_comments').val("");
		}
	});
}

function doChangeDesc(desc) {
	$.ajax({
		url: "/ajax/changeUserDesc.php",
		type: "POST",
		data: {
			description: desc
		},
		success: function( data ) {
			//console.log(data);
			$('#changeDescStatus').html("<font color=green>Changed.</font>"); // Clear status
			$('#changeDescStatus').hide(3000);
		}
	});
}
