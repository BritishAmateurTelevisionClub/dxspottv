<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Repeater Admin</title>
</head>
<body>
<?php
$logged_in = false;
$is_admin = false;
if(isset($_COOKIE["user_id"]) && isset($_COOKIE["session_key"])) {
	require_once('dxspottv_pdo.php');
	$sessions_stmt = $dbc->prepare("SELECT COUNT(1) FROM sessions WHERE user_id=? AND session_id=?;");
	$sessions_stmt->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
	$sessions_stmt->bindValue(2, $_COOKIE["session_key"]);
	$sessions_stmt->execute();
	if($sessions_stmt->rowCount()!=0) {
        $logged_in = true;
        $auth_stmt = $dbc->prepare("SELECT repeater_admin FROM users WHERE id=?;");
        $auth_stmt->bindValue(1, $_COOKIE["user_id"], PDO::PARAM_INT);
        $auth_stmt->execute();
        $auth_stmt->bindColumn(1, $is_admin_n);
        $auth_stmt->fetch();
        if($is_admin_n==1) {
            $is_admin = true;
        }
    }
}
if($logged_in) {
if($is_admin) {
?>
<h1>Repeater Admin</h1>
<ul>
<?php
require_once('dxspottv_pdo.php');
$repeaters_stmt = $dbc->prepare("SELECT id,callsign FROM all_repeaters;");
$repeaters_stmt->execute();
$repeaters_stmt->bindColumn(1, $current_id);
$repeaters_stmt->bindColumn(2, $current_callsign);
while($repeaters_stmt->fetch()) {
?>
<li><a href="editRepeater.html?id=<?php print $current_id; ?>"><?php print $current_callsign; ?></a>
<?php
}
?>
</ul>
<?php
} else { // Logged In, not allowed
?>
</head>
<body>
<h2 style="color: red;">You are not a Repeater Admin. Please contact <a href="mailto:dxspottv.feedback@gmail.com">dxspottv.feedback@gmail.com</a> for access</h2>
<?php
}
} else { // Not Logged In
?>
</head>
<body>
<h2 style="color: red;">You are not logged in, please <a href="/">Log In</a> and try again.</h2>
<?php
}
?>
</body>
</html>
