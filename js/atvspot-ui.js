var months = ["_dummy_", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
// Set up Time/Band dropdowns
//
var valTimeSpan;
//var bandDict = {70cm: 1, 23cm: 2, 13cm: 3, 9cm: 5, 6cm: 6, 3cm: 4};
var valBandChoice = {}; // Gets setup on .ready()
var spotAutocomplete = [];
$(document).ready(function()
{
    $('#time_select').change(function()
    {
        setTimeSpan($('#time_select').val());
        checkSpots();
        checkUsers();
        checkRepeaters();
    });
    $('#band_select').change(function()
    {
        setBandChoice($('#band_select').val());
        checkSpots();
            checkUsers();
        checkRepeaters();
    });
    setTimeSpan($('#time_select').val());
    setBandChoice($('#band_select').val());
    checkSpots();
    checkUsers();
    checkRepeaters();
});

// Spot Form
var validSpotForm;
$(document).ready(function()
{
    $('#remote_callsign').keyup(function()
    {
        var remoteCallsign = $('#remote_callsign').val().toUpperCase();
        if($.inArray(remoteCallsign,spotAutocomplete)>=0)
        {
            var locator_search = $.grep(user_markers, function(e){ return e.callsign == remoteCallsign; });
            if (locator_search.length==0)
            {
                var locator_search = $.grep(repeater_markers, function(e){ return e.callsign == remoteCallsign; });
            }
            if (locator_search.length!=0)
            {
                $('#remote_loc').val(locator_search[0].locator);
            }
        }
    });
    $('#spot_button').button().click( function()
    {
        if($('#remote_callsign').val().length<4)
        {
            $('#submitStatus').show();
            $('#submitStatus').html("<font color=red>Need callsign.</font>");
        }
        else if($('#remote_loc').val().length<6)
        {
            $('#submitStatus').show();
            $('#submitStatus').html("<font color=red>Need locator.</font>");
        }
        else
        {
            submitSpot();
        }
    });
});
// Set up Users/Repeaters checkboxes
//
$(document).ready(function()
{
    // Both checkboxes checked
    $('#repeaterBox').prop('checked', true);
    // So show all to start! (done in map load callback)
    // Then functions for if changed
    $('#repeaterBox').change(function()
    {
        infowindow.close();
        checkRepeaters();
    });
});

// Load IRC (using php-configured url)
//
$(document).ready(function()
{
    if(logged_in)
    {
        document.getElementById('irc_frame').src = irc_frame_source;
    }
});

// Set up login/logout buttons
//
$(document).ready(function()
{
    // submit form if enter is pressed
    $('#callsign_input').keypress(function(e)
    {
        if(e.which == 10 || e.which == 13)
        {
            doLogin();
        }
    });
    $('#passwd_input').keypress(function(e)
    {
        if(e.which == 10 || e.which == 13)
        {
            doLogin();
        }
    });
    $('#login_button').button().click( function()
    {
            doLogin();
    });
    $('#logout_button').button().click( function()
    {
            window.location.href = "/ajax/logout.php";
    });
    $('#register_button').button().click( function()
    {
            window.location.href = "/register/";
    });
    
    // Set up tabs
    $( "#tabs" ).tabs();

    if(auth_error)
    {
        $('#auth-error-text').text(auth_error_text);
        $('#auth-error-box').show();
    }
});

// Radio active status
$(document).ready(function()
{
    $('#radioBox').change(function()
    {
        $('#changeRadioStatus').show();
        $('#changeRadioStatus').html("<font color=green>Changing..</font>");
        if($('#radioBox').is(":checked"))
        {
            radio_status = 1;
        }
        else
        {
            radio_status = 0;
        }
        doChangeRadio(radio_status);
    });
});

// Station Description Edit Function
var pos_marker;
$(document).ready(function()
{
    $('#desc_button').button().click( function()
    {
        $('#changePosStatus').fadeOut(500);
        google.maps.event.clearListeners(map, 'click');
        if (typeof pos_marker != 'undefined')
        {
            pos_marker.setMap(null);
        }
        doChangeDesc($('#station_description_edit').val(), $('#station_website_edit').val(), $('#station_lat_edit').val(),$('#station_lon_edit').val());
    });
    $('#setposition_button').button().click( function()
    {
        google.maps.event.addListener(map, 'click', function(event)
        {
            $('#changePosStatus').html("<font color=green>Click map again to change, or 'Save' below to set position.</font>");
            $('#station_lat_edit').val(event.latLng.lat());
            $('#station_lon_edit').val(event.latLng.lng());
            placeMarker(event.latLng);
        });
        $('#changePosStatus').html("<font color=green>Click on the map to set your location.</font>");
    });
});

function placeMarker(location)
{
  if(pos_marker)
  {
    pos_marker.setPosition(location);
  }
  else
  {
    pos_marker = new google.maps.Marker({
      position: location,
      map: map
    });
  }
}

var latestSpot = 0;
function createGlobalSpotLog(spotLog)
{
    if(Number(spotLog[0].i)<= latestSpot)
    {
        return;
    }

    latestSpot = Number(spotLog[0].i);

    var spotLogDivContent = "";
    if(spotLog.length!=0)
    {
        var spot = new Array();
        for(s_id in spotLog)
        {
            if(spotLog[s_id].seconds_ago>604800) break;
            if(s_id=="last") break;
            var spot = spotLog[s_id];
            var primary_search = $.grep(user_markers, function(e)
            {
                return e.user_id == spot.p;
            });
            // find our secondary marker
            if(spot.sr==1)
            { // if its a repeater
                var secondary_search = $.grep(repeater_markers, function(e)
                {
                    return e.repeater_id == spot.s;
                });
            }
            else
            { // or a user
                var secondary_search = $.grep(user_markers, function(e)
                {
                    return e.user_id == spot.s;
                });
            }
            spotLogDivContent+=parseInt(spot['t'].substr(8,2),10)+"&nbsp;"+months[parseInt(spot['t'].substr(5,2))]+"&nbsp;"+spot['t'].substr(11,8)+":&nbsp;<b>"+primary_search[0].callsign+"</b>-><b>"+secondary_search[0].callsign+"</b>";
            spotLogDivContent+="&nbsp;"+bandFromID(parseInt(spot.b));
            if(spot['c'].length != 0)
            {
                spotLogDivContent+="<br>";
                spotLogDivContent+="<i>"+spot['c']+"</i>";
            }
            spotLogDivContent+="<br><br>";
        }
    }
    else
    {
        spotLogDivContent="No spots found.";
    }
    $('#spotLog').html(spotLogDivContent);
}

function loadSpotAutocomplete()
{
    var callsigns = new Array();
    for (var i=0; i<user_markers.length; i++)
    {
        callsigns.push(user_markers[i].callsign);
    }
    for (var i=0; i<repeater_markers.length; i++)
    {
        callsigns.push(repeater_markers[i].callsign);
    }
    spotAutocomplete = callsigns;
    $("#remote_callsign").autocomplete({
      source: spotAutocomplete
    });
    $("#search_callsign").autocomplete({
      source: spotAutocomplete
    });
}
$(document).ready(function()
{
    $('#search_button').button().click( function()
    {
        wantedCallsign = $("#search_callsign").val().toUpperCase();
        var user_search = $.grep(user_markers, function(e){ return e.callsign == wantedCallsign; });
        if(user_search.length==0)
        {
            var repeater_search = $.grep(repeater_markers, function(e){ return e.callsign == wantedCallsign; });
            if(repeater_search.length==0)
            {
                $('#findResults').html('No results found.');
            }
            else
            {
                repeater_index = $.inArray(repeater_search[0], repeater_markers);
                repeater_Desc = "<b>Callsign:</b>&nbsp;"+repeater_markers[repeater_index].callsign+"<br>"+
                    "<b>Locator:</b>&nbsp;"+repeater_markers[repeater_index].qth_r+"<br>"+
                    "<b>Location:</b>&nbsp;"+repeater_markers[repeater_index].qth;
                var freqDesc = '<b>TX:</b>&nbsp;'+repeater_markers[repeater_index].tx_freq+'MHz<br>'+
                    '<b>RX</b>:&nbsp;'+repeater_markers[repeater_index].rx_freq+'MHz<br>';
                if (typeof repeater_markers[repeater_index].rx_freq_2 != 'undefined')
                {
                    freqDesc += '<b>RX:</b>&nbsp;'+repeater_markers[repeater_index].rx_freq_2+'MHz<br>';
                }
                if (typeof repeater_markers[repeater_index].alt_tx_freq != 'undefined')
                {
                    freqDesc += '<br><b>TX:</b>&nbsp;'+repeater_markers[repeater_index].alt_tx_freq+'MHz<br>'+
                        '<b>RX:</b>&nbsp;'+repeater_markers[repeater_index].alt_rx_freq+'MHz<br>';
                    if (typeof repeater_markers[repeater_index].alt_rx_freq_2 != 'undefined')
                    {
                        freqDesc += '<b>RX:</b>&nbsp;'+repeater_markers[repeater_index].alt_rx_freq_2+'MHz<br>';
                    }
                }
                repeater_Desc += "<br><br>"+freqDesc+"<br>";
                if(repeater_markers[repeater_index].description!='')
                {
                    repeater_Desc += "<b>Repeater Description:</b><br><pre style='white-space: pre-wrap;'>"+repeater_markers[repeater_index].description+"</pre>";
                }
                repeater_Desc += "<b>Website:</b>&nbsp;"+'<a href="'+repeater_markers[repeater_index].website+'" target="_blank">'+repeater_markers[repeater_index].website+'</a>';
                $('#findResults').html(repeater_Desc);
                map.panTo(repeater_markers[repeater_index].position);
                map.setZoom(9);
            }
        }
        else
        {
            user_index = $.inArray(user_search[0], user_markers);
            user_Desc = "<b>Callsign:</b>&nbsp;"+user_markers[user_index].callsign+"<br>"+
                "<b>Locator:</b>&nbsp;"+user_markers[user_index].locator+"<br><br>";
            if(user_markers[user_index].known==1)
            {
                if (user_markers[user_index].activity>86400)
                {
                    activeString = Math.round(user_markers[user_index].activity/86400) + ' days ago.';
                }
                else if (user_markers[user_index].activity>3600)
                {
                    activeString = Math.round(user_markers[user_index].activity/3600) + ' hours ago.';
                }
                else if (user_markers[user_index].activity>60)
                {
                    activeString = Math.round(user_markers[user_index].activity/60) + ' minutes ago.';
                }
                else
                {
                    activeString = '<font color="green">Currently Active.</font>'
                }
                user_Desc +="<b>Last Seen:</b>&nbsp;"+activeString+"<br><br>";
            }
            user_Desc +="<b>Station Description:</b><br><pre style='white-space: pre-wrap;'>"+user_markers[user_index].station_desc+"</pre>"+
                "<b>Website:</b>&nbsp;"+'<a href="'+user_markers[user_index].station_website+'" target="_blank">'+user_markers[user_index].station_website+'</a>';
            $('#findResults').html(user_Desc);
            user_markers[user_index].setVisible(true);
            map.panTo(user_markers[user_index].position);
            map.setZoom(9);
        }
    });
});

// Elevation Profile Dialog
var profile_distance;
var profile_user_latlng;
var profile_remote_latlng;
$(document).ready(function()
{
    $( "#elevationDialog" ).dialog({ autoOpen: false, width: 900, height: 300 });
    $( "#elevationDialog" ).on( "dialogclose", function( event, ui )
    {
        profile_path.setMap(null);
    });
    $('#curvatureBox').change(function()
    {
        drawPath(profile_user_latlng, profile_remote_latlng);
    });
});

function elevation_profile(callsignUser, latUser, lonUser, callsignRemote, latRemote, lonRemote)
{
    $('#spanChartFrom').html(callsignUser+" ("+latUser+", "+lonUser+")");
    $('#spanChartTo').html(callsignRemote+" ("+latRemote+", "+lonRemote+")");
    profile_user_latlng = new google.maps.LatLng(latUser, lonUser);
    profile_remote_latlng = new google.maps.LatLng(latRemote, lonRemote);
    profile_distance = google.maps.geometry.spherical.computeDistanceBetween(profile_user_latlng, profile_remote_latlng);
    drawPath(profile_user_latlng, profile_remote_latlng);
    $( "#elevationDialog" ).dialog( "open" );
}

function drawPath(user_station, remote_station)
{

  // Create a new chart in the elevation_chart DIV.
  chart = new google.visualization.LineChart(document.getElementById('elevationChart'));

  var path = [user_station, remote_station];

  // Create a PathElevationRequest object using this array.
  // Ask for 256 samples along that path.
  var pathRequest = {
    'path': path,
    'samples': 256
  }

  // Initiate the path request.
  elevator.getElevationAlongPath(pathRequest, plotElevation);
}

// Takes an array of ElevationResult objects, draws the path on the map
// and plots the elevation profile on a Visualization API ColumnChart.
function plotElevation(results, status)
{
    var numSamples = 256;
    var earthRadius = 6378100; // in metres
    if (status != google.maps.ElevationStatus.OK)
    {
        return;
    }
    var elevations = results;

    // Extract the elevation samples from the returned results
    // and store them in an array of LatLngs.
    var elevationPath = [];
    for (var i = 0; i < results.length; i++)
    {
        elevationPath.push(elevations[i].location);
    }

    // Display a polyline of the elevation path.
    var pathOptions = {
        path: elevationPath,
        strokeColor: '#0000CC',
        opacity: 0.4,
        map: map
    }
    if(profile_path)
    {
        profile_path.setOptions(pathOptions);
    }
    else
    {
        profile_path = new google.maps.Polyline(pathOptions);
    }

    // Extract the data from which to populate the chart.
    // Because the samples are equidistant, the 'Sample'
    // column here does double duty as distance along the
    // X axis.
    var earthRadiusSquared = Math.pow(earthRadius,2);
    var max_deviation = 0-(Math.sqrt(earthRadiusSquared - Math.pow((results.length/2)*(profile_distance/numSamples),2)) - earthRadius);
    var startAlt = elevations[0].elevation; // Set these to user elevation + mast height
    var endAlt = elevations[results.length-1].elevation; //
    var data = new google.visualization.DataTable();
    data.addColumn('number', 'Distance (km)');
    data.addColumn('number', 'Elevation (m)');
    data.addColumn('number', 'Path');
    for (var i = 0; i < results.length; i++)
    {
        rdistance = ((i/results.length)*profile_distance)/1000;
        if(i<(results.length/2))
        {
            distance = ((results.length/2)-i)*(profile_distance/numSamples);
        }
        else
        {
            distance = (i-(results.length/2))*(profile_distance/numSamples);
        }
        pathAlt = startAlt + ((i/results.length)*(endAlt-startAlt));
        if ($('#curvatureBox').is(":checked"))
        {
            deviation = max_deviation + (Math.sqrt(earthRadiusSquared - Math.pow(distance,2)) - earthRadius);
            data.addRow([Math.round(rdistance*10)/10, Math.round(elevations[i].elevation+deviation), Math.round(pathAlt)]);
        } else {
            data.addRow([Math.round(rdistance*10)/10, Math.round(elevations[i].elevation), Math.round(pathAlt)]);
        }
    }
    // Draw the chart using the data within its DIV.
    document.getElementById('elevationChart').style.display = 'block';
    chart.draw(data, {
        height: 150,
        legend: 'none',
        pointSize: 1,
        hAxis: {gridlines: {color: '#333'}, title: "km"},
        series: {
            1:{
                color: 'black',
                pointSize: 1
            },
            2:{
                color: 'green',
                pointSize: 2
            }
        }
    });
}
