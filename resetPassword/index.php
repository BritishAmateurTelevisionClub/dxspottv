<html>
<?php
session_start();

if(isset($_REQUEST['key']))
{
  $rl_count = apc_fetch('dxspot_resetPassword_rl',$rl_exists);
  if($rl_exists && $rl_count >= 10)
  {
    ?>
<body>
<h3>Too many failed requests, please try again later.</h3>
</body>
    <?php
    exit();
  }

  require_once('../dxspottv_pdo.php');

  $key_stmt = $dbc->prepare("SELECT id from users WHERE resetKey=?;");
  $key_stmt->bindValue(1, $_REQUEST["key"], PDO::PARAM_STR);
  $key_stmt->execute();
  $key_stmt->bindColumn(1, $user_id);
  if($key_stmt->rowCount()==1)
  {
    $key_stmt->fetch();
?>
<body>
 <div>
  <h2>Reset Password</h2>
  <b>New Password</b>
  <input type='password' id='input-password1'></input><br />
  <b>Repeat Password</b>
  <input type='password' id='input-password2'></input>
  <br />
  <button id='input-button'>Submit</button>
  <br />
  <div id='input-status'></span>
 </div>
</body>
<script src="/lib/jquery-3.2.1.min.js"></script>
<script>
<?php print "var resetKey = \"".$_REQUEST["key"]."\";"; ?>

$(document).ready(function()
{
    $('#input-button').click(submitForm);
    $('#input-password1').keypress(function(e)
    {
        if(e.which == 10 || e.which == 13)
        {
            submitForm();
        }
    });
    $('#input-password2').keypress(function(e)
    {
        if(e.which == 10 || e.which == 13)
        {
            submitForm();
        }
    });
});
function submitForm()
{
    $("#input-status").text("");
    if($("#input-password1").val()=="")
    {
        $("#input-status").text("Error: Enter a new password");
        return false;
    }
    if($("#input-password1").val()!=$("#input-password2").val())
    {
        $("#input-status").text("Error: Passwords do not match");
        return false;
    }
    $.ajax({
        url: "/ajax/resetPassword.php",
        type: "POST",
        dataType: "json",
        data: {
            key: resetKey,
            passwd: $("#input-password1").val()
        }, 
        success: function(data)
        {
            if(data.error==0)
            {
                $("#input-status").html("Reset successful! Click <a href=\"/\">here</a> to return to DXSpot.TV and log in.");
            }
            else
            {
                $("#input-status").text(data.message);
            }
        }
    });
}
</script>
</html>
<?php
    exit();
  }
  else
  {
    ?>
<body>
<h3>Reset key not recognised.</h3>
</body>
    <?php
    apc_inc('dxspot_resetPassword_rl',1,$success);
    if($success==false)
    {
        apc_store('dxspot_resetPassword_rl',1,5);
    }
    exit();
  }
}
?>
<body>
 <div>
  <h2>Reset Password</h2>
  <b>Callsign</b>
  <input type='text' id='input-username'></input>
  <br />
  <button id='input-button'>Submit</button>
  <br />
  <div id='input-status'></div>
 </div>
</body>
<script src="/lib/jquery-3.2.1.min.js"></script>
<script>
$(document).ready(function()
{
    $('#input-button').click(submitForm);
    $('#input-username').keypress(function(e)
    {
        if(e.which == 10 || e.which == 13)
        {
            submitForm();
        }
    });
});
function submitForm()
{
    $("#input-status").text("");
    if($("#input-username").val()=="")
    {
        $("#input-status").text("Error: Enter a callsign");
        return false;
    }
    $.ajax({
        url: "/ajax/resetAuth.php",
        type: "POST",
        dataType: "json",
        data: {
            callsign: $("#input-username").val()
        }, 
        success: function(data)
        {
            $("#input-status").text(data.message);
        }
    });
}
</script>
</html>
