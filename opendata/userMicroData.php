<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>ATV Users</title>
</head>
<body>
<h1>Users</h1>
<?php
require_once('dxspottv_pdo.php');

$data_stmt = $dbc->prepare("SELECT callsign,lat,lon,locator,station_desc,website FROM users;");
$data_stmt->execute();
$data_stmt->bindColumn(1, $callsign);
$data_stmt->bindColumn(2, $lat);
$data_stmt->bindColumn(3, $lon);
$data_stmt->bindColumn(4, $locator);
$data_stmt->bindColumn(5, $description);
$data_stmt->bindColumn(6, $website);

while ($data_stmt->fetch()) {
	print '<div itemscope itemtype="station">';
	print '<h2 itemprop="callsign" itemtype="http://schema.org/Text">'.$callsign.'</h2>';
	print '<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
	print 'Latitude: <span itemprop="latitude">'.$lat.'</span><br>';
	print 'Longitude: <span itemprop="longitude">'.$lon.'</span><br>';
	print '</div>';
	print 'Locator: <span itemprop="maidenhead" itemtype="http://schema.org/Text">'.$locator.'</span><br>';
	if($description!="") print 'Station Description: <span itemprop="description" itemtype="http://schema.org/Text">'.$description.'</span><br>';
	if($website!=null) print '<a itemprop="website" itemtype="http://schema.org/URL" href="http://'.$website.'">'.$website.'</a><br>';
	print '</div>';
}
?>
</body>
</html>
