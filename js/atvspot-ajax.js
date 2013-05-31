// Set up refresh functions
//
var userSpotRefresh;
var repeaterRefresh;

if(logged_in) {
	var activityRefresh=self.setInterval(function(){updateActivity()},5000+Math.round(Math.random()*400)); // Add from 0-400ms randomly
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
}

function getUserSpotData() {
	$.ajax({
		url: "/ajax/userSpotData.php",
		success: function( data ) {
			myJSONObject = eval('(' + data + ')');
    		parseUsers(myJSONObject['users']);
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
}

function getRepeaterData() {
	$.ajax({
		url: "/ajax/repeaterData.php",
		success: function( data ) {
			myJSONObject = eval('(' + data + ')');
    		parseRepeaters(myJSONObject);
    		
    		setTimeSpan($('#time_select').val());
			setBandChoice($('#band_select').val());
			checkSpots();
			checkUsers();
			checkRepeaters();
		
    		loadSpotAutocomplete();
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
			$('#submitStatus').show();
			$('#submitStatus').fadeOut(1500);
			// Now clear all the boxes
			$('#remote_callsign').val("");
			$('#remote_loc').val("");
			$('#spot_comments').val("");
		}
	});
}

function doChangeDesc(desc, website, lat, lon) {
	$.ajax({
		url: "/ajax/changeUserDesc.php",
		type: "POST",
		data: {
			description: desc,
			website: website,
			lat: lat,
			lon: lon
		},
		success: function( data ) {
			//console.log(data);
			$('#changeDescStatus').html("<font color=green>Changed.</font>"); // Clear status
			$('#changeDescStatus').show();
			$('#changeDescStatus').fadeOut(1500);
		}
	});
}

function getUserVars() {
	$.ajax({
		url: "/ajax/getUserInfo.php",
		success: function( data ) {
			//console.log(data);
			userData = eval('(' + data + ')');
			user_callsign = userData['callsign'];
			user_lat = userData['lat'];
			user_lon = userData['lon'];
			user_desc = userData['description'];
			user_website = userData['website'];
			$('#station_description_edit').val(user_desc);
			$('#station_website_edit').val(user_website);
			$('#station_lat_edit').val(user_lat);
			$('#station_lon_edit').val(user_lon);
		}
	});
}
