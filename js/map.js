	// == shows all markers of a particular category, and ensures the checkbox is checked ==
      function show(category) {
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
        // == check the checkbox ==
        document.getElementById(category+"box").checked = true;
      }

      // == hides all markers of a particular category, and ensures the checkbox is cleared ==
      function hide(category) {
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

        // == clear the checkbox ==
        document.getElementById(category+"box").checked = false;
        // == close the info window, in case its open on a marker that we just hid
        infowindow.close();
      }

      // == a checkbox has been clicked ==
      function boxclick(box,category) {
        if (box.checked) {
          show(category);
        } else {
          hide(category);
        }
        // == rebuild the side bar
        //makeSidebar();
      }

      function myclick(i) {
        google.maps.event.trigger(gmarkers[i],"click");
      }

