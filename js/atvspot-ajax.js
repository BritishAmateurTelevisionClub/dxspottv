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

function submitListen() {
	var active70cm;
	var active23cm;
	var active13cm;
	if($('#listen_70cm_box').is(":checked")) {
		active70cm = 1;
	} else {
		active70cm = 1;
	}
	if($('#listen_23cm_box').is(":checked")) {
		active23cm = 1;
	} else {
		active23cm = 1;
	}
	if($('#listen_13cm_box').is(":checked")) {
		active13cm = 1;
	} else {
		active13cm = 1;
	}
	$.ajax({
		url: "/ajax/submitListen.php",
		type: "GET",
		data: {
			active70cm: active70cm,
			active23cm: active23cm,
			active13cm: active13cm
		},
		success: function( data ) {
			console.log(data);
		}
	});
}
