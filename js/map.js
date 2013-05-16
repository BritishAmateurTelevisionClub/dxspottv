      function mapShow(category) {
        for (var i=0; i<repeater_markers.length; i++) {
          if (repeater_markers[i].mycategory == category) {
            repeater_markers[i].setVisible(true);
          }
	}
        for (var i=0; i<user_markers.length; i++) {
          if (user_markers[i].mycategory == category) {
            user_markers[i].setVisible(true);
          }
        }
        document.getElementById(category+"Box").checked = true;
      }

      function mapHide(category) {
        for (var i=0; i<repeater_markers.length; i++) {
          if (repeater_markers[i].mycategory == category) {
            repeater_markers[i].setVisible(false);
          }
        }
        for (var i=0; i<user_markers.length; i++) {
          if (user_markers[i].mycategory == category) {
            user_markers[i].setVisible(false);
          }
        }

        document.getElementById(category+"Box").checked = false;
        infowindow.close();
      }

function usersShow() {
	changeUsersBandSelect($('#band_select').val());
	checkSpots();
	infowindow.close();
}
function usersHide() {
	for (var i=0; i<user_markers.length; i++) {
		user_markers[i].setVisible(false);
	}
	checkSpots();
	infowindow.close();
}

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
		// find our primary user marker
		var primary_search = $.grep(user_markers, function(e){
			return e.user_id == spot_lines[i].primary_id;
		});
		// find our secondary marker
		if(spot_lines[i].secondary_isrepeater==1) { // if its a repeater
			var secondary_search = $.grep(repeater_markers, function(e){
				return e.repeater_id == spot_lines[i].secondary_id;
			});
		} else { // or a user
			var secondary_search = $.grep(user_markers, function(e){
				return e.user_id == spot_lines[i].secondary_id;
			});
		}
		// if both ends are visible then show(), else hide()
		if((primary_search[0].visible==true) && (secondary_search[0].visible==true)) {
			spot_lines[i].setVisible(true);
		} else {
			spot_lines[i].setVisible(false);
		}
	}
}

function changeUsersSelect(select_val) {
	switch(select_val)
	{
	case "70cm":
		for (var i=0; i<user_markers.length; i++) {
			if(user_markers[i].is70cm==1 && user_markers[i].activity<=valTimeSpan) {
				user_markers[i].setVisible(true);
			} else {
				user_markers[i].setVisible(false);
			}
		}
		break;
	case "23cm":
		for (var i=0; i<user_markers.length; i++) {
			if(user_markers[i].is23cm==1 && user_markers[i].activity<=valTimeSpan) {
				user_markers[i].setVisible(true);
			} else {
				user_markers[i].setVisible(false);
			}
		}
		break;
	case "13cm":
		for (var i=0; i<user_markers.length; i++) {
			if((user_markers[i].is13cm==1 || user_markers[i].is3cm==1) && user_markers[i].activity<=valTimeSpan) {
				user_markers[i].setVisible(true);
			} else {
				user_markers[i].setVisible(false);
			}
		}
		break;
	default: // All
		for (var i=0; i<user_markers.length; i++) {
			if(user_markers[i].activity<=valTimeSpan) {
				user_markers[i].setVisible(true);
			} else {
				user_markers[i].setVisible(false);
			}
		}
		break;
	}
	checkSpots();
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

