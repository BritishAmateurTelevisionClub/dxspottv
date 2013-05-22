function repeatersShow() {
	changeRepeatersBandSelect($('#band_select').val());
	checkSpots();
	infowindow.close();
}
function repeatersHide() {
	for (var i=0; i<repeater_markers.length; i++) {
		repeater_markers[i].setVisible(false);
	}
	checkSpots();
	infowindow.close();
}

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
    var show_online_users = $('#userBox').is(":checked");
    for (var i=0; i<user_markers.length; i++) {
			if(user_markers[i].known=="1" && user_markers[i].activity<=60 && show_online_users) { // Online (in last minute) and online is ticked
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

function changeRepeatersBandSelect(select_val) {
	switch(select_val)
	{
	case "70cm":
		for (var i=0; i<repeater_markers.length; i++) {
			if(repeater_markers[i].is70cm==1) {
				repeater_markers[i].setVisible(true);
			} else {
				repeater_markers[i].setVisible(false);
			}
		}
		break;
	case "23cm":
		for (var i=0; i<repeater_markers.length; i++) {
			if(repeater_markers[i].is23cm==1) {
				repeater_markers[i].setVisible(true);
			} else {
				repeater_markers[i].setVisible(false);
			}
		}
		break;
	case "13cm":
		for (var i=0; i<repeater_markers.length; i++) {
			if(repeater_markers[i].is13cm==1 || repeater_markers[i].is3cm==1) {
				repeater_markers[i].setVisible(true);
			} else {
				repeater_markers[i].setVisible(false);
			}
		}
		break;
	default: // All
		for (var i=0; i<repeater_markers.length; i++) {
			repeater_markers[i].setVisible(true);
		}
		break;
	}
	checkSpots();
}

function myclick(i) {
	google.maps.event.trigger(gmarkers[i],"click");
}

