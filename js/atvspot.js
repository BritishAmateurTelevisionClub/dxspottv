	if(!Array.prototype.last) {
	    Array.prototype.last = function() {
	        return this[this.length - 1];
	    }
	}
	var repeater_markers = [];
	var user_markers = [];
        var map;

	var infowindow;
	var session_id;
	var logged_in;
	function initialize() {
		var mapOptions = {
        		zoom: 6,
        		center: new google.maps.LatLng(51.5, -1.39),
        		mapTypeId: google.maps.MapTypeId.ROADMAP
        	};

       		map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		infowindow = new google.maps.InfoWindow(
		{
			size: new google.maps.Size(150,50)
		});

		google.maps.event.addListener(map, 'click', function() {
		        infowindow.close();
	        });

		blueIcon = new google.maps.MarkerImage("https://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png");
		redIcon = new google.maps.MarkerImage("https://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png");

		getRepeaters();
		getUsers();

		show("user");
		show("70cm");
		show("23cm");
		show("13cm");
		show("3cm");
      	}

	function getMarkerImage(iconColor) {
   		if ((typeof(iconColor)=="undefined") || (iconColor==null)) {
			iconColor = "red";
		}
		if (!gicons[iconColor]) {
			gicons[iconColor] = new google.maps.MarkerImage("http://admissions.mansfield.edu/more/visit-mansfield/interactive-map/map/maps/pin-"+ iconColor +"2.png",
				// This marker is 20 pixels wide by 34 pixels tall.
				new google.maps.Size(30, 30),
				// The origin for this image is 0,0.
				new google.maps.Point(0,0),
				// The anchor for this image is at 6,20.
				new google.maps.Point(9, 30));
		}
		return gicons[iconColor];
	}

	function createUserMarker(latlng,name,html,category) {
		var contentString = html;
		var marker = new google.maps.Marker({
		        position: latlng,
			icon: redIcon,
			//icon: mapicons[category],
		        map: map,
		        title: name
        		//zIndex: Math.round(latlng.lat()*-100000)<<5
	        });
	        marker.mycategory = category;
	        marker.myname = name;
	        user_markers.push(marker);

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent(name+"<br>"+contentString);
		        infowindow.open(map,marker);
        	});
	}

	function createRepeaterMarker(latlng,name,html,category) {
		var contentString = html;
		var marker = new google.maps.Marker({
		        position: latlng,
			icon: blueIcon,
			//icon: mapicons[category],
		        map: map,
		        title: name
        		//zIndex: Math.round(latlng.lat()*-100000)<<5
	        });
	        marker.mycategory = category;
	        marker.myname = name;
	        repeater_markers.push(marker);

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent(name+"<br>"+contentString);
		        infowindow.open(map,marker);
        	});
	}

	function parseRepeaters(JSONinput) {
		var r_id = new Array();
		for(r_id in JSONinput){
			var repeater = JSONinput[r_id];
			createRepeaterMarker(new google.maps.LatLng(repeater['latitude'], repeater['longitude']),repeater['callsign'],repeater['description'],repeater['band']);
		}
    	}

	function parseUsers(JSONinput) {
		var u_id = new Array();
		for(u_id in JSONinput){
			var user = JSONinput[u_id];
			var activity_str;
			if(user['months_active']>0) {
				activity_str = 'Last active ' + user['months_active'] + ' months ago.';
			} else if (user['days_active']>0) {
				activity_str = 'Last active ' + user['days_active'] + ' days ago.';
			} else if (user['hours_active']>0) {
				activity_str = 'Last active ' + user['hours_active'] + 'hours ago.';
			} else {
				activity_str = 'Currently Active.'
			}
			createUserMarker(new google.maps.LatLng(user['latitude'], user['longitude']),user['callsign'],activity_str,"users");
		}
    	}

	function getRepeaters() {
	var JsonObject = {};
	var http = new XMLHttpRequest();
	http.open("GET", "/atvspot/ajax/repeaters.php", true);
	http.onreadystatechange = function () {
	   if (http.readyState == 4 && http.status == 200) {
    		var responseTxt = http.responseText;
    		myJSONObject = eval('(' + responseTxt + ')');
    		parseRepeaters(myJSONObject);
 	   }
	}
	http.send(null);
	}

	function getUsers() {
	var JsonObject = {};
	var http = new XMLHttpRequest();
	http.open("GET", "/atvspot/ajax/users.php", true);
	http.onreadystatechange = function () {
	   if (http.readyState == 4 && http.status == 200) {
    		var responseTxt = http.responseText;
    		myJSONObject = eval('(' + responseTxt + ')');
    		parseUsers(myJSONObject);
 	   }
	}
	http.send(null);
	}

      function loadScript() {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
            'callback=initialize';
        document.body.appendChild(script);
        $('#irc_frame').attr('src', $irc_frame_source);
      }

      window.onload = loadScript;
