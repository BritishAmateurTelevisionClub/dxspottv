// Set up refresh functions
//
var mapRefresh=self.setInterval(function(){updateMap()},5000);

var activityRefresh=self.setInterval(function(){updateActivity()},18000);

function updateMap() {
	console.log("Updating map..");
	getUsers();
	getRepeaters();
	getSpots();
	ga('send', 'event', 'refresh', 'Map Data');
}

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

function getRepeaters() {
	$.ajax({
		url: "/ajax/repeaters.php",
		type: "GET",
		data: {
			bands: $("band_select").val()
		},
		success: function( data ) {
			//console.log(data);
			myJSONObject = eval('(' + data + ')');
    		parseRepeaters(myJSONObject);
		}
	});
}

function getUsers() {
	$.ajax({
		url: "/ajax/users.php",
		type: "GET",
		data: {
			timespan: $("time_select").val(),
			bands: $("band_select").val()
		},
		success: function( data ) {
			//console.log(data);
			myJSONObject = eval('(' + data + ')');
    		parseUsers(myJSONObject);
		}
	});
}

function getSpots() {	
	$.ajax({
		url: "/ajax/spots.php",
		success: function( data ) {
			//console.log(data);
			myJSONObject = eval('(' + data + ')');
    		parseSpots(myJSONObject);
    		createGlobalSpotLog(myJSONObject);
		}
	});
}

function updateActivity() {	
	$.ajax({
		url: "/ajax/update_activity.php",
		success: function( data ) {
			//console.log(data);
		}
	});
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
			freq: $("#spot_freq").val(),
			mode: $("#spot_mode_select").val(),
			r_callsign: $("#remote_callsign").val(),
			r_locator: $("#remote_loc").val(),
			r_lat: rlatlon[0],
			r_lon: rlatlon[1],
			comments: $("#spot_comments").val()
		},
		success: function( data ) {
			//console.log(data);
			$('#submitStatus').val("Submitted."); // Clear status
			$('#submitStatus').hide(800);
			// Now clear all the boxes
			$('#remote_callsign').val("");
			$('#remote_loc').val("");
			$('#spot_comments').val("");
		}
	});
}
