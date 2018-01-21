var marker;
function placeMarker(location)
{
    if(marker)
    {
        marker.setPosition(location);
    }
    else
    {
        marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }
}

function initialize()
{
    google.maps.visualRefresh = true;
    var mapOptions = {
        zoom: 4,
        center: new google.maps.LatLng(50.5, 0),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl: false
    };

    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

    google.maps.event.addListener(map, 'click', function(event)
    {
        $('#lat').val(roundTo(event.latLng.lat(),4));
        $('#lon').val(roundTo(event.latLng.lng(),4));
        placeMarker(event.latLng);
    });
}

var input_password = document.getElementById('passwd');
var password_bar = document.getElementById('passwd-bar');
input_password.addEventListener('keyup', function()
{
  var analysis = zxcvbn(input_password.value);
  switch(analysis.score)
  {
    case 0:
      password_bar.style.backgroundImage = 'linear-gradient(to right, #ff0000, #ff0000 20%, #ffffff 20%)';
      break;
    case 1:
      password_bar.style.backgroundImage = 'linear-gradient(to right, #df4500, #df4500 40%, #ffffff 40%)';
      break;
    case 2:
      password_bar.style.backgroundImage = 'linear-gradient(to right, #d0a500, #d0a500 60%, #ffffff 60%)';
      break;
    case 3:
      password_bar.style.backgroundImage = 'linear-gradient(to right, #c3e35c, #c3e35c 80%, #ffffff 80%)';
      break;
    case 4:
      password_bar.style.backgroundImage = 'linear-gradient(to right, #a3ff5c, #a3ff5c 100%, #ffffff 100%)';
      break;
  }
});

var button_lock=false; // To prevent double-click
$(document).ready(function()
{
    $("#validationFailDialog").dialog({ autoOpen: false });
    $("#captchaFailDialog").dialog({ autoOpen: false });
    $('#register_form').validate({
        submitHandler: function(form) { }
    });
    $('#register_button').button().click( function()
    {
        if($("#register_form").valid()==true)
        {
            if(!button_lock)
            {
                button_lock = true;
                $.ajax({
                    url: '/ajax/submitRegister.php',
                    type: "POST",
                    data: {
                        fname: $('#fname').val(),
                        callsign: $('#callsign').val(),
                        passwd: $('#passwd').val(),
                        email: $('#email').val(),
                        locator: CoordToLoc(parseFloat($('#lat').val()),parseFloat($('#lon').val())),
                        lat: $('#lat').val(),
                        lon: $('#lon').val(),
                        recaptcha: grecaptcha.getResponse()
                    },
                    success: function( data )
                    {
                        //console.log(data);
                        button_lock = false;
                        $("#submitStatus").html('');
                        var returnJSON = eval('(' + data + ')');
                        if(returnJSON['successful']==1)
                        {
                            $('#first_form').hide();
                            $('#successMessage').show();
                        }
                        else
                        {
                            $('#first_form').show();
                            switch(returnJSON['error'])
                            {
                                case 1:
                                    $("#captchaFailDialog").dialog("open");
                                    break;

                                case 2:
                                    alert("A database error occurred, please try again.");
                                    break;

                                case 3:
                                    alert("A User Account already exists for this callsign.");
                                    break;

                                default:
                                   alert("An unknown error occurred, please try again."); 
                            }
                        }
                    }
                });
                $("#submitStatus").html('<font color="green"><b>Submitting...</b></font>');
                grecaptcha.reset();
            }
        }
    });
    $('#return_button').button().click( function()
    {
        window.location.href = "/";
    });
});

function roundTo(value, decimal_places)
{
    return Number(Math.round(value+'e'+decimal_places)+'e-'+decimal_places);
}
