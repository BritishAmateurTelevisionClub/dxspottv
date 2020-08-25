
if(logged_in)
{
    updateActivity();
}

function doLogin()
{
    var login_callsign = $("#callsign_input").val();
    if(login_callsign=="")
    {
        $('#auth-error-text').text("Callsign required.");
        $('#auth-error-box').show();
        return;
    }
    var login_password = $('#passwd_input').val();
    if(login_password=="")
    {
        $('#auth-error-text').text("Password required.");
        $('#auth-error-box').show();
        return;
    }
    $.ajax({
        url: "/ajax/login.php",
        type: "POST",
        dataType: "json",
        data: {
            callsign: login_callsign,
            passwd: login_password
        },
        success: function( data )
        {
            if(data.error == 0)
            {
                location.reload(true);
            }
            else
            {
                $('#passwd_input').val("");
                $('#auth-error-text').text(data.message);
                $('#auth-error-box').show();
            }
        }
    });
}

function getUserSpotData()
{
    $.ajax({
        url: "/ajax/userSpotRefresh.php",
        success: function( data )
        {
            updateUsers(data['users']);
            if(data['spots'].length!=0)
            {
                parseSpots(data['spots']);
                createGlobalSpotLog(data['spots']);
            }
            
            setTimeSpan($('#time_select').val());
            setBandChoice($('#band_select').val());
            checkSpots();
            checkUsers();
            checkRepeaters();
        
            loadSpotAutocomplete();
            setTimeout(getUserSpotData, 2000+Math.round(Math.random()*500));
        }
    });
}


function updateActivity()
{
    $.ajax({
        url: "/ajax/update_activity.php",
        success: function( data )
        {
            //console.log(data);
            setTimeout(updateActivity,3000+Math.round(Math.random()*500));
        }
    });
}

function submitSpot()
{
    var rlatlon = [];
    rlatlon = LoctoLatLon($("#remote_loc").val());
    $('#submitStatus').val("Submitting...");
    $('#submitStatus').show();
    $.ajax({
        url: "/ajax/submit_spot.php",
        type: "POST",
        data: {
            band_id: $("#spot_band_select").val(),
            mode: $("#spot_mode_select").val(),
            r_callsign: $("#remote_callsign").val(),
            r_locator: $("#remote_loc").val(),
            r_lat: rlatlon[0],
            r_lon: rlatlon[1],
            comments: $("#spot_comments").val()
        },
        success: function( data )
        {
            //console.log(data);
            myJSONObject = eval('(' + data + ')');
            if(myJSONObject['success']=="1")
            {
                $('#submitStatus').html("<font color=green>Submitted.</font>");
                $('#submitStatus').show();
                $('#submitStatus').fadeOut(1500);
                // Now clear all the boxes
                $('#remote_callsign').val("");
                $('#remote_loc').val("");
                $('#spot_comments').val("");
            }
            else
            { // There was an error
                switch(myJSONObject['error'])
                {
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

function doChangeDesc(desc, website, lat, lon)
{
    var locator = CoordToLoc(parseFloat(lat),parseFloat(lon));
    $.ajax({
        url: "/ajax/changeUserDesc.php",
        type: "POST",
        data: {
            description: desc,
            website: website,
            lat: lat,
            lon: lon,
            loc: locator
        },
        success: function( data )
        {
            //console.log(data);
            $('#changeDescStatus').html("<font color=green>Changed.</font>"); // Clear status
            $('#changeDescStatus').show();
            $('#changeDescStatus').fadeOut(1500);
        }
    });
}

function doChangeRadio(status)
{
    $.ajax({
        url: "/ajax/changeUserRadio.php",
        type: "POST",
        data: {
            radio_active: status
        },
        success: function( data )
        {
            //console.log(data);
            $('#changeRadioStatus').html("<font color=green>Changed.</font>");
            $('#changeRadioStatus').fadeOut(500);
        }
    });
}


function getUserVars()
{
    if(logged_in)
    {
        $.ajax({
            url: "/ajax/getUserInfo.php",
            dataType: "json",
            success: function( userData )
            {
                if(userData['error'] === undefined)
                {
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
                init_semaphores['userdata'] = true;
                init_gate();
            }
        });
    }
    else
    {
        init_semaphores['userdata'] = true;
        init_gate();
    }
}
