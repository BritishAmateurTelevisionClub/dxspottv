var repeater_markers = [];
var user_markers = [];
var spot_lines = [];
var map;
var elevator;
var profile_path;

var infowindow;
var session_id;
var logged_in;

var mapData;
var init_semaphores = [];
init_semaphores['map'] = false;
init_semaphores['mapdata'] = false;
init_semaphores['userdata'] = false;

$(function()
{
    $.ajax({
        url: "https://dxspot.batc.org.uk/ajax/mapData.php",
        success: function( data )
        {
            mapData = data;
            init_semaphores['mapdata'] = true;
            init_gate();
        }
    });
    getUserVars();
});


function init_gate()
{
    if(Object.keys(init_semaphores).every(function(el){ return init_semaphores[el]; }))
    {
        parseMapData(mapData);
        mapData = null;
        if(logged_in)
        {
            map.setOptions({ zoom: 6, center: new google.maps.LatLng(user_lat, user_lon) });
        }
        setTimeout(getUserSpotData, 2000);
    }
}

google.load("visualization", "1", {packages:["corechart"]});

// Callback from Google Maps Script Load
//
function init_map()
{
    google.maps.visualRefresh = true;
    var mapOptions = {
        zoom: 3,
        mapTypeId: google.maps.MapTypeId.TERRAIN,
        streetViewControl: false
    };
    
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    map.setOptions({ center: new google.maps.LatLng(44.7, -46) });

    infowindow = new google.maps.InfoWindow( {
            size: new google.maps.Size(150,50)
    });
    
    elevator = new google.maps.ElevationService();

    google.maps.event.addListener(map, 'click', function()
    {
        infowindow.close();
    });
    
    google.maps.event.addListener(map, "rightclick", function(event)
    {
        var randomLoc = CoordToLoc(event.latLng.lat(), event.latLng.lng());
        infoContent="<h3 style='line-height: 0.3em;'>"+randomLoc+"</h3>";
        if(logged_in)
        {
            var user_latlng = new google.maps.LatLng(user_lat, user_lon);
            var elevation_vars = "'"+user_callsign+"','"+user_lat+"','"+user_lon+"','"+randomLoc+"','"+event.latLng.lat()+"','"+event.latLng.lng()+"'";
            infoContent+='<br>'+
                '<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, event.latLng)))+'&deg;<br>'+
                '<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, event.latLng)/1000)*10)/10+'km<br>'+
                '<a href="javascript:elevation_profile('+elevation_vars+')"><b>Path Elevation Profile</b></a>';
        }
        infowindow.setContent(infoContent);
        infowindow.setPosition(event.latLng);
        infowindow.open(map);
    });

    userActiveIcon = new google.maps.MarkerImage("/images/active_user.ico");
    userAwayIcon = new google.maps.MarkerImage("/images/away_user.ico");
    userUnknownIcon = new google.maps.MarkerImage("/images/unknown_user.ico");
    repeaterIcon = new google.maps.MarkerImage("/images/active_repeater.ico");
    repeaterOfflineIcon = new google.maps.MarkerImage("/images/inactive_repeater.ico");

    init_semaphores['map'] = true;
    init_gate();
}

function createUserMarker(user_data)
{
    var lat_lon = new google.maps.LatLng(user_data['la'], user_data['lo']);
    
    var marker = new google.maps.Marker({
        position: lat_lon,
        map: map,
        title: user_data['c']
    });
    
    if(user_data['act']>18)
    { // 18 seconds, should check in every 5 seconds
        marker.setOptions( {
            icon: userUnknownIcon, // white icon, if shown (spotted)
            zIndex: 11
        });
    }
    else if(user_data['ra']==1)
    {
        marker.setOptions( {
            icon: userActiveIcon, // green
            zIndex: 13
        });
    }
    else
    {
        marker.setOptions( {
            icon: userAwayIcon, // yellow
            zIndex: 12
        });
    }
    
    marker.user_id = user_data['i']
    marker.callsign = user_data['c'];
    marker.locator = user_data['loc'];
    marker.activity = user_data['act'];
    marker.known = user_data['k'];
    marker.station_desc = user_data['sd'];
    if(user_data['w']!='')
    {
        marker.station_website = "http://"+user_data['w'];
    }
    else
    {
        marker.station_website = '';
    }
    user_markers.push(marker);
    
    var infoTab = '<div class="user_bubble_info">'+
        '<h3 style="line-height: 0.3em;">'+marker.callsign+'</h3>'+
        '<b>'+marker.locator+'</b>';
    if(logged_in && (user_callsign!=user_data['c']))
    {
        var user_latlng = new google.maps.LatLng(user_lat, user_lon);
        var elevation_vars = "'"+user_callsign+"','"+user_lat+"','"+user_lon+"','"+user_data['c']+"','"+user_data['la']+"','"+user_data['lo']+"'";
        infoTab+='<br><br>'+
            '<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, lat_lon)))+'&deg;<br>'+
            '<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, lat_lon)/1000)*10)/10+'km<br>'+
            '<a href="javascript:elevation_profile('+elevation_vars+')"><b>Path Elevation Profile</b></a>';
    }
    infoTab += '</div>';
    var infoBubble = new InfoBubble({
        maxWidth: 150,
        minWidth: 150,
        maxHeight: 110,
        minHeight: 110,
        shadowStyle: 0,
        padding: 8,
        backgroundColor: '#fff',
        borderRadius: 8,
        arrowSize: 10,
        borderWidth: 1,
        borderColor: '#ccc',
        disableAutoPan: true,
        hideCloseButton: false,
        arrowPosition: 50,
        arrowStyle: 0
    });
    
    infoBubble.addTab('<span class="bubble_label">Info</span>', infoTab);
    if(marker.station_desc != '' || marker.station_website != '')
    {
        var descTab = '<div class="user_bubble_desc">'+
        marker.station_desc;
        if(marker.station_website!='') 
        {
            descTab += '<br><br><a href="'+marker.station_website+'" target="_blank"><b>'+marker.station_website+'</b></a>';
        }
        descTab += '</div>';
        infoBubble.addTab('<span class="bubble_label">Description</span>', descTab);
    }

    google.maps.event.addListener(marker, 'click', function()
    {
        if (!infoBubble.isOpen())
        {
            infoBubble.open(map, marker);
        }
    });
}

function updateUserMarker(user_data, user_index)
{
    if(user_data['act']>18)
    { // 18 seconds, should check in every 5 seconds
        user_markers[user_index].setOptions( {
            icon: userUnknownIcon, // white icon, if shown (spotted)
            zIndex: 11
        });
    }
    else if(user_data['ra']==1)
    {
        user_markers[user_index].setOptions( {
            icon: userActiveIcon, // green
            zIndex: 13
        });
    }
    else
    {
        user_markers[user_index].setOptions( {
            icon: userAwayIcon, // yellow
            zIndex: 12
        });
    }
    user_markers[user_index].activity = user_data['act'];
}

function createRepeaterMarker(repeater_data)
{
    var latlon = new google.maps.LatLng(repeater_data['lat'], repeater_data['lon']);
    
    var marker = new google.maps.Marker({
        position: latlon,
        map: map,
        title: repeater_data['qrz']
    });
    
    if(typeof repeater_data['op'] != 'undefined')
    {
        marker.setOptions( {
            icon: repeaterIcon,
            zIndex: 9
        });
    }
    else
    {
        marker.setOptions( {
            icon: repeaterOfflineIcon,
            zIndex: 8
        });
    }
    
    marker.repeater_id = repeater_data['id'];
    marker.lat = repeater_data['lat'];
    marker.lon = repeater_data['lon'];
    marker.callsign = repeater_data['qrz'];
    if(typeof repeater_data['qth']!='undefined') marker.qth = repeater_data['qth'];
    marker.locator = repeater_data['loc'];
    
    if (typeof repeater_data['tx1'] != 'undefined') marker.tx1 = repeater_data['tx1'];
    if (typeof repeater_data['tx2'] != 'undefined') marker.tx2 = repeater_data['tx2'];
    if (typeof repeater_data['tx3'] != 'undefined') marker.tx3 = repeater_data['tx3'];
    if (typeof repeater_data['tx4'] != 'undefined') marker.tx4 = repeater_data['tx4'];
    if (typeof repeater_data['tx5'] != 'undefined') marker.tx5 = repeater_data['tx5'];
    if (typeof repeater_data['tx6'] != 'undefined') marker.tx6 = repeater_data['tx6'];
    if (typeof repeater_data['tx7'] != 'undefined') marker.tx7 = repeater_data['tx7'];
    if (typeof repeater_data['tx8'] != 'undefined') marker.tx8 = repeater_data['tx8'];
    if (typeof repeater_data['tx9'] != 'undefined') marker.tx9 = repeater_data['tx9'];
    
    if (typeof repeater_data['rx1'] != 'undefined') marker.rx1 = repeater_data['rx1'];
    if (typeof repeater_data['rx2'] != 'undefined') marker.rx2 = repeater_data['rx2'];
    if (typeof repeater_data['rx3'] != 'undefined') marker.rx3 = repeater_data['rx3'];
    if (typeof repeater_data['rx4'] != 'undefined') marker.rx4 = repeater_data['rx4'];
    if (typeof repeater_data['rx5'] != 'undefined') marker.rx5 = repeater_data['rx5'];
    if (typeof repeater_data['rx6'] != 'undefined') marker.rx6 = repeater_data['rx6'];
    if (typeof repeater_data['rx7'] != 'undefined') marker.rx7 = repeater_data['rx7'];
    if (typeof repeater_data['rx8'] != 'undefined') marker.rx8 = repeater_data['rx8'];
    if (typeof repeater_data['rx9'] != 'undefined') marker.rx9 = repeater_data['rx9'];
    
    marker.is2m = 0;
    marker.is70cm = 0;
    marker.is23cm = 0;
    marker.is13cm = 0;
    marker.is9cm = 0;
    marker.is6cm = 0;
    marker.is3cm = 0;
    if (typeof repeater_data['2m']!='undefined') marker.is2m = 1;
    if (typeof repeater_data['70cm']!='undefined') marker.is70cm = 1;
    if (typeof repeater_data['23cm']!='undefined') marker.is23cm = 1;
    if (typeof repeater_data['13cm']!='undefined') marker.is13cm = 1;
    if (typeof repeater_data['9cm']!='undefined') marker.is9cm = 1;
    if (typeof repeater_data['6cm']!='undefined') marker.is6cm = 1;
    if (typeof repeater_data['3cm']!='undefined') marker.is3cm = 1;
    
    marker.desc = repeater_data['desc']
    if (typeof repeater_data['www'] != 'undefined')
    {
        marker.website = repeater_data['www']
    }
    else
    {
        marker.website = '';
    }
    if(typeof repeater_data['keep'] != 'undefined')
    {
        marker.keeper = repeater_data['keep']
    }
    else
    {
        marker.keeper = '';
    }
    repeater_markers.push(marker);
    
    repeater_data = null;
    
    var infoTab = '<div class="repeater_bubble_info">'+
        '<h3 style="line-height: 0.3em;">'+marker.callsign+'</h3>'+
        '<b>'+marker.locator+'</b>';
    if(typeof marker.qth!='undefined') infoTab += '&nbsp;-&nbsp;'+marker.qth;
    if(logged_in)
    {
        var user_latlng = new google.maps.LatLng(user_lat, user_lon);
        var elevation_vars = "'"+user_callsign+"','"+user_lat+"','"+user_lon+"','"+marker.callsign+"','"+marker.lat+"','"+marker.lon+"'";
        infoTab+='<br><br>'+
            '<b>Bearing:</b>&nbsp;'+Math.round(convertHeading(google.maps.geometry.spherical.computeHeading(user_latlng, latlon)))+'&deg;<br>'+
            '<b>Distance:</b>&nbsp;'+Math.round((google.maps.geometry.spherical.computeDistanceBetween(user_latlng, latlon)/1000)*10)/10+'km<br>'+
            '<a href="javascript:elevation_profile('+elevation_vars+')"><b>Path Elevation Profile</b></a>';
    }
    infoTab += '</div>';
    var freqTab = '<div class="repeater_bubble_freq">';
    
    if (typeof marker.tx1 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx1+'MHz</b><br>';
    if (typeof marker.tx2 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx2+'MHz</b><br>';
    if (typeof marker.tx3 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx3+'MHz</b><br>';
    if (typeof marker.tx4 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx4+'MHz</b><br>';
    if (typeof marker.tx5 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx5+'MHz</b><br>';
    if (typeof marker.tx6 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx6+'MHz</b><br>';
    if (typeof marker.tx7 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx7+'MHz</b><br>';
    if (typeof marker.tx8 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx8+'MHz</b><br>';
    if (typeof marker.tx9 != 'undefined') freqTab += '<b>TX:&nbsp;'+marker.tx9+'MHz</b><br>';
    
    if (typeof marker.rx1 != 'undefined') freqTab += '<br><b>RX:&nbsp;'+marker.rx1+'MHz</b><br>';
    if (typeof marker.rx2 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx2+'MHz</b><br>';
    if (typeof marker.rx3 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx3+'MHz</b><br>';
    if (typeof marker.rx4 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx4+'MHz</b><br>';
    if (typeof marker.rx5 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx5+'MHz</b><br>';
    if (typeof marker.rx6 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx6+'MHz</b><br>';
    if (typeof marker.rx7 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx7+'MHz</b><br>';
    if (typeof marker.rx8 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx8+'MHz</b><br>';
    if (typeof marker.rx9 != 'undefined') freqTab += '<b>RX:&nbsp;'+marker.rx9+'MHz</b><br>';
    
    freqTab += '</div>';
    
    var descTab = '<div class="repeater_bubble_desc">';
    if ( marker.desc != '') descTab += marker.desc+'<br><br>';
    if (marker.keeper != '') descTab += '<b>Keeper:</b>&nbsp;'+marker.keeper+'<br><br>';
    if (marker.website != '') descTab += '<a href="'+marker.website+'" target="_blank"><b>Repeater Website</b></a>';
    descTab += '</div>';
    
    var infoBubble = new InfoBubble({
        maxWidth: 180,
        minWidth: 180,
        maxHeight: 110,
        minHeight: 110,
        shadowStyle: 0,
        padding: 8,
        backgroundColor: '#fff',
        borderRadius: 8,
        arrowSize: 10,
        borderWidth: 1,
        borderColor: '#ccc',
        disableAutoPan: true,
        hideCloseButton: false,
        arrowPosition: 50,
        arrowStyle: 0
    });
    
    infoBubble.addTab('<span class="bubble_label">Info</span>', infoTab);
    infoBubble.addTab('<span class="bubble_label">Tx/Rx</span>', freqTab);
    infoBubble.addTab('<span class="bubble_label">Description</span>', descTab);

    google.maps.event.addListener(marker, 'click', function()
    {
        if (!infoBubble.isOpen())
        {
            infoBubble.open(map, marker);
        }
    });
}

function createSpotLine(spot_data)
{
    var primary_search = $.grep(user_markers, function(e){ return e.user_id == spot_data['p']; });
    var primary_latlon = primary_search[0].position;
    var primary_callsign = primary_search[0].callsign;
    if(spot_data['sr']==0)
    {
        var secondary_search = $.grep(user_markers, function(e){ return e.user_id == spot_data['s']; });
    }
    else
    {
        var secondary_search = $.grep(repeater_markers, function(e){ return e.repeater_id == spot_data['s']; });
    }
    
    var secondary_latlon = secondary_search[0].position;
    var secondary_callsign = secondary_search[0].callsign;
    
    var spotLineCoordinates = [
        primary_latlon,
        secondary_latlon
    ];
    var spotLine = new google.maps.Polyline({
        path: spotLineCoordinates,
        strokeOpacity: 1.0,
        strokeWeight: 3,
        geodesic: true,
        zIndex: 1
    });
    
    switch(Number(spot_data['b']))
    {
        case 1: // 70cm
                case 7: // 2m
            spotLine.setOptions({
                strokeColor: "#FF0000" //red
            });
            break
        case 2: // 23cm
                case 3: // 13cm
                case 5: // 9cm
                case 6: // 6cm
            spotLine.setOptions({
                strokeColor: "#FFA500", //orange
                strokeWeight: 4 // thicker line
            });
            break
        default: //13 cm and above
            spotLine.setOptions({
                strokeColor: "#0404B4" //blue
            });
            break
    }
    
    switch(Number(spot_data['m']))
    {
        case 0: // Not defined - assume Digital
            spotLine.setOptions({
                zIndex: 5
            });
            spotLine.mode = "Digital ATV";
            break;
        case 1: // Analog TV
            spotLine.setOptions({
                zIndex: 4
            });
            spotLine.mode = "Analog ATV";
            break;
        case 2: // Digital TV (WB)
            spotLine.setOptions({
                zIndex: 5
            });
            spotLine.mode = "Digital ATV";
            break;
        case 3: // Beacon
            var lineSymbol = {
                path: 'M 0,-0.5 0,0.5',
                strokeOpacity: 0.5,
                scale: 2
            };
            spotLine.setOptions({
                strokeOpacity: 0,
                icons: [{
                    icon: lineSymbol,
                    offset: '0',
                    repeat: '10px'
                }],
                zIndex: 3
            });
            spotLine.mode = "NB Beacon";
            break;
    }
    
    spotLine.spot_id = spot_data['i'];
    spotLine.band_id = spot_data['b'];
    spotLine.mode_id = spot_data['m'];
    
    spotLine.primary_id = spot_data['p'];
    spotLine.primary_callsign = primary_callsign;
    spotLine.secondary_id = spot_data['s'];
    spotLine.secondary_callsign = secondary_callsign;
    spotLine.secondary_isrepeater = spot_data['sr']
    spotLine.time = spot_data['t'];
    spotLine.ago = spot_data['rt'];
        spotLine.comments = spot_data['c'];
    spotLine.date = parseInt(spot_data['t'].substr(8,2))+"&nbsp;"+months[parseInt(spot_data['t'].substr(5,2))]+"&nbsp;"+spot_data['t'].substr(11,8);    
    spotLine.distance = Math.round((google.maps.geometry.spherical.computeDistanceBetween(primary_latlon, secondary_latlon)/1000)*10)/10;
    
    var infoContent = spotLine.date+"<br><b>"+primary_callsign+"</b>&nbsp;->&nbsp;"+"<b>"+secondary_callsign+"</b><br>"+bandFromID(spotLine.band_id)+"&nbsp;<i>"+spotLine.mode+"</i><br><i>"+spotLine.comments+"</i><br>"+spotLine.distance+"&nbsp;km";
    
    google.maps.event.addListener(spotLine, 'click', function()
    {
        infowindow.setContent(infoContent);
        infowindow.setPosition(new google.maps.LatLng((primary_latlon.lat() + secondary_latlon.lat())/2, (primary_latlon.lng() + secondary_latlon.lng())/2));
        infowindow.open(map);
    });
    
    spotLine.setMap(map);
    spot_lines.push(spotLine);
}

function parseMapData(data)
{
    loadUsers(data['users']);
    parseRepeaters(data['repeaters']);
    parseSpots(data['spots']);
    createGlobalSpotLog(data['spots']);
    
    setTimeSpan($('#time_select').val());
    setBandChoice($('#band_select').val());
    checkSpots();
    checkUsers();
    checkRepeaters();

    loadSpotAutocomplete();
}

function parseRepeaters(JSONinput)
{
    var r_id = new Array();
    for(r_id in JSONinput)
    {
        if(JSONinput[r_id].length!=0)
        {
            createRepeaterMarker(JSONinput[r_id]);
        }
    }
}

function loadUsers(JSONinput)
{
    var u_id = new Array();
    for(u_id in JSONinput)
    {
        var user = JSONinput[u_id];
        if(user.length!=0)
        {
            createUserMarker(user);
        }
    }
}

function updateUsers(JSONinput)
{
    var u_id = new Array();
    for(u_id in JSONinput)
    {
        var user = JSONinput[u_id];
        if(user.length!=0)
        {
            var marker_search = $.grep(user_markers, function(e){ return e.user_id == user['i']; });
            if(marker_search.length==1)
            {
                user_index = $.inArray(marker_search[0], user_markers);
                updateUserMarker(user, user_index);
            }
            else if (marker_search.length==0)
            { // New user
                createUserMarker(user);
            }
        }
    }
}

function parseSpots(JSONinput)
{
    var s_id = new Array();
    for(s_id in JSONinput)
    {
        var spot = JSONinput[s_id];
        var spot_search = $.grep(spot_lines, function(e){ return e.spot_id == spot['i']; });
        if(spot_search.length==0 && spot.length!=0)
        {
            createSpotLine(spot);
        }
    }
}
