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

      function myclick(i) {
        google.maps.event.trigger(gmarkers[i],"click");
      }

