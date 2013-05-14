// Set up refresh functions
//
var mapRefresh=self.setInterval(function(){updateMap()},5000);

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

function submitSpot() {
	var rlatlon = [];
	rlatlon = LoctoLatLon($("#remote_loc").val());
	ga('send', 'event', 'action', 'Submit Spot');
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
		}
	});
}

function updateListening() {
	var active70cm;
	var active23cm;
	var active13cm;
	if($('#listen_70cm_box').is(":checked")) {
		active70cm = 1;
	} else {
		active70cm = 0;
	}
	if($('#listen_23cm_box').is(":checked")) {
		active23cm = 1;
	} else {
		active23cm = 0;
	}
	if($('#listen_13cm_box').is(":checked")) {
		active13cm = 1;
	} else {
		active70cm = 0;
	}
	ga('send', 'event', 'action', 'Update Listening');
	$.ajax({
		url: "/ajax/update_listening.php",
		type: "GET",
		data: {
			l70cm: active70cm,
			l70cm_freq: $('#listen_70cm_freq').val(),
			l23cm: active23cm,
			l23cm_freq: $('#listen_23cm_freq').val(),
			l13cm: active13cm,
			l13cm_freq: $('#listen_13cm_freq').val()
		},
		success: function( data ) {
			//console.log(data);
		}
	});
}
