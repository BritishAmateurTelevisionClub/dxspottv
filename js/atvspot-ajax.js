// Set up refresh functions
//
var mapRefresh=self.setInterval(function(){updateMap()},3000);

function updateMap() {
	console.log("Updating map..");
	getUsers();
	getRepeaters();
	//getSpots();
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
		type: "GET",
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
		type: "GET",
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
