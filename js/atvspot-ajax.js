// Set up refresh timers
//
$(document).ready(function() {
	// Update Map objects every 2 seconds
	window.setInterval(updateMap(),2000);
});

function updateMap() {
	console.log("Updating map..");
	getUsers();
	getRepeaters();
	//getSpots();
}

function getRepeaters() {
	$.ajax({
		url: "/ajax/repeaters.php",
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
		data: {
			timespan: $("time_select").val(),
			bands: $("band_select").val()
		},
		success: function( data ) {
			console.log(data);
		}
	});
}

function submitSpot() {
	$.ajax({
		url: "/ajax/submitSpot.php",
		data: {
			freq: $("spot_freq").val(),
			mode: $("spot_mode_select").val(),
			remoteCallsign: $("remote_callsign").val(),
			remoteLocator: $("remote_loc").val(),
			comments: $("spot_comments").val()
		},
		success: function( data ) {
			console.log(data);
		}
	});
}

function submitListen() {
	var active70cm = $('#listen_70cm_box').is(":checked");
	var active23cm = $('#listen_23cm_box').is(":checked");
	var active13cm = $('#listen_13cm_box').is(":checked");
	$.ajax({
		url: "/ajax/submitListen.php",
		data: {
			70cm: active70cm
		},
		success: function( data ) {
			console.log(data);
		}
	});
}
