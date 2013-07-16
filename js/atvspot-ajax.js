// Set up refresh functions
//
var userSpotRefresh;
var repeaterRefresh;

if(logged_in) {
	var activityRefresh=self.setInterval(function(){updateActivity()},3000+Math.round(Math.random()*400)); // Add from 0-400ms randomly
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

function getUserSpotData() {
	$.ajax({
		url: "http://www.dxspot.tv/api/userSpotRefresh",
		success: function( data ) {
    		updateUsers(data['users']);
    		parseSpots(data['spots']);
    		createGlobalSpotLog(data['spots']);
    		
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
		url: "http://www.dxspot.tv/api/repeaterData",
		success: function( data ) {
    		parseRepeaters(data);
    		
    		setTimeSpan($('#time_select').val());
			setBandChoice($('#band_select').val());
			checkSpots();
			checkUsers();
			checkRepeaters();
		
    		loadSpotAutocomplete();
		}
	});
	ga('send', 'event', 'refresh', 'Repeater Data');
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
			myJSONObject = eval('(' + data + ')');
			if(myJSONObject['success']=="1") {
				$('#submitStatus').html("<font color=green>Submitted.</font>");
				$('#submitStatus').show();
				$('#submitStatus').fadeOut(1500);
				// Now clear all the boxes
				$('#remote_callsign').val("");
				$('#remote_loc').val("");
				$('#spot_comments').val("");
			} else { // There was an error
				switch(myJSONObject['error']) {
					case "1": // Data Missing
						$('#submitStatus').html("<font color=red>Error: Data Missing.</font>");
						$('#submitStatus').show();
						$('#submitStatus').fadeOut(1500);
						break;
					case "2": // Session not found
						$('#submitStatus').html("<font color=red>Error: Session not found.</font>");
						$('#submitStatus').show();
						$('#submitStatus').fadeOut(1500);
						break;
					case "3": // Spotted yourself
						$('#submitStatus').html("<font color=red>Error: Can't spot yourself.</font>");
						$('#submitStatus').show();
						$('#submitStatus').fadeOut(1500);
						break;
					default:
						$('#submitStatus').html("<font color=red>Unknown Error</font>");
						$('#submitStatus').show();
						$('#submitStatus').fadeOut(1500);
						break;
				}
			}
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

function doChangeRadio(status) {
	$.ajax({
		url: "/ajax/changeUserRadio.php",
		type: "POST",
		data: {
			radio_active: status
		},
		success: function( data ) {
			//console.log(data);
			$('#changeRadioStatus').html("<font color=green>Changed.</font>");
    		$('#changeRadioStatus').fadeOut(500);
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
			user_radioactive = userData['radio_active'];
			$('#station_description_edit').val(user_desc);
			$('#station_website_edit').val(user_website);
			$('#station_lat_edit').val(user_lat);
			$('#station_lon_edit').val(user_lon);
			if(user_radioactive==1) {
				$('#radioBox').prop('checked',true);
			} else {
				$('#radioBox').prop('checked',false);
			}
		}
	});
}
