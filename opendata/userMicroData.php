<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
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
	print '<h2 itemprop="callsign">'+$callsign+'</h2>';
	print '<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
	print 'Latitude: <span itemprop="latitude">'+$lat+'</span><br>';
	print 'Longitude: <span itemprop="longitude">'+$lon+'</span><br>';
	print '</div>';
	print 'Locator: <span itemprop="maidenhead">'+$locator+'</span><br>';
	if($description!="") print 'Station Description: <span itemprop="description">'+$description+'</span><br>';
	if($website!=null) print '<a itemprop="website" href="http://'+$website+'">'+$website+'</a><br>';
	print '</div>';
}
?>
</body>
</html>
