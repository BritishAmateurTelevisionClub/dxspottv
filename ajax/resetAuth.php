<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-6.0.3/src/Exception.php';
require 'PHPMailer-6.0.3/src/PHPMailer.php';
require 'PHPMailer-6.0.3/src/SMTP.php';

function sendEmail($to, $subject, $htmlBody, $textBody) {

    include('../credentials.php');

    $mail = new PHPMailer(false);
    //Server settings
    $mail->SMTPDebug = 0; // 2: Enable verbose debug output
    $mail->isSMTP();
    $mail->Host = $smtp_credentials['host'];
    $mail->SMTPAuth = $smtp_credentials['auth'];
    $mail->Username = $smtp_credentials['user'];
    $mail->Password = $smtp_credentials['password'];
    $mail->SMTPSecure = $smtp_credentials['security']; // 'tls' / 'ssl'
    $mail->Port = $smtp_credentials['port'];
    
    //Recipients
    $mail->setFrom('dxspottv@batc.org.uk', 'DXSpot.TV');
    $mail->addAddress($to);
    $mail->addReplyTo('phil@philcrump.co.uk', 'DXSpot.TV Admin');
    
    //Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $htmlBody;
    $mail->AltBody = $textBody;
    
    $mail->send();
}

$rl_count = apc_fetch('dxspot_resetAuth_rl',$rl_exists);
if($rl_exists && $rl_count >= 10)
{  
   print(json_encode(array('error' => 1, 'message' => 'Too many failed attempts, please wait a few seconds.')));
   exit();
}

require_once('../dxspottv_pdo.php');

$return_data = array();
if(isset($_REQUEST["callsign"]))
{
    $key_stmt = $dbc->prepare("SELECT resetKey, email from users WHERE callsign=?;");
    $key_stmt->bindValue(1, $_REQUEST["callsign"], PDO::PARAM_STR);
    $key_stmt->execute();
    $key_stmt->bindColumn(1, $existing_key);
    $key_stmt->bindColumn(2, $user_email);
    if($key_stmt->rowCount()==1)
    {
        $key_stmt->fetch();
        if(empty($existing_key) || $existing_key=="")
        {
            $key = random_alphanumeric(32);
            $update_stmt = $dbc->prepare("UPDATE users SET resetKey=? WHERE callsign=?;");
            $update_stmt->bindValue(1, $key, PDO::PARAM_STR);
            $update_stmt->bindValue(2, $_REQUEST["callsign"], PDO::PARAM_STR);
            $update_stmt->execute();
            sendEmail(
                $user_email,
                'DXSpot.TV - Password Reset Requested',
                "Hi ".$_REQUEST["callsign"].",<br>
<br>
You, or someone claiming to be you, has requested a password reset on your account at <a href=\"https://www.dxspot.tv/\" target=\"_blank\">DXSpot.TV</a>.<br>
<br>
To complete this password reset, please set your new password at <a href=\"https://www.dxspot.tv/resetPassword/?key=".$key."\" target=\"_blank\">https://www.dxspot.tv/resetPassword/?key=".$key."</a><br>
<br>
If this was not you, then please ignore this email and nothing will be changed.<br>
<br>
Regards,<br>
BATC Administration Team",
                "Hi ".$_REQUEST["callsign"].",

You, or someone claiming to be you, has requested a password reset on your account at DXSpot.TV.

To complete this password reset, please set your new password at https://www.dxspot.tv/resetPassword/?key=".$key."

If this was not you, then please ignore this email and nothing will be changed.

Regards,
BATC Administration Team"
            );
            $return_data = array('error' => 0, 'message' => 'Email Sent');
        }
        else
        {
            $return_data = array('error' => 1, 'message' => 'A password reset has already been requested, please contact the administrator if the email doesn\'t arrive after a few minutes.');
        }
    }
    else
    {
        $return_data = array('error' => 1, 'message' => 'Callsign not recognised');
    }
}
else
{
    $return_data = array('error' => 1, 'message' => 'Bad Request');
}

if($return_data['error']==1)
{
    apc_inc('dxspot_resetAuth_rl',1,$success);
    if($success==false)
    {
        apc_store('dxspot_resetAuth_rl',1,10);
    }
}
print(json_encode($return_data));
?>
