function checkSpots() {
    for (var i=0; i<spot_lines.length; i++)
    {
        if(valBandChoice[spot_lines[i].band_id] && (spot_lines[i].ago<=valTimeSpan))
        {
            spot_lines[i].setVisible(true);
        }
        else
        {
            spot_lines[i].setVisible(false);
        }
    }
}


function checkUsers() {
    for (var i=0; i<user_markers.length; i++)
    {
        if(user_markers[i].known=="1" && user_markers[i].activity<=60)
        { // Online (in last minute)
            user_markers[i].setVisible(true); // then show
        }
        else
        { // Are they part of a shown spot?
            // Grep spot lines for user_id
            var spot_search = $.grep(spot_lines, function(e)
            {
                return (e.primary_id == user_markers[i].user_id || (e.secondary_id == user_markers[i].user_id && e.secondary_isrepeater == 0));
            });
            var visibleToBe = false;
            for (var j=0; j<spot_search.length; j++)
            {
                if (spot_search[j].visible)
                {
                    visibleToBe = true;
                }
            }
            if(visibleToBe)
            {
                user_markers[i].setVisible(true);
            }
            else
            {
                user_markers[i].setVisible(false);
            }
        }
    }
}

function checkRepeaters()
{
    var repeater_select = $('#repeaterBox').is(":checked");
    var band_select = $('#band_select').val();
    for (var i=0; i<repeater_markers.length; i++)
    {
        var visibleToBe = false;
        if(repeater_select)
        {
            visibleToBe = true;
        }
        else
        { // Are they part of a shown spot?
            // Grep spot lines for user_id
            var spot_search = $.grep(spot_lines, function(e)
            {
                return ((e.secondary_isrepeater == '1') && e.secondary_id == repeater_markers[i].repeater_id);
            });
            for (var j=0; j<spot_search.length; j++)
            {
                if (spot_search[j].visible)
                {
                    visibleToBe = true;
                }
            }
        }
        if(visibleToBe)
        {
            repeater_markers[i].setVisible(true);
        }
        else
        {
            repeater_markers[i].setVisible(false);
        }
    }
}
