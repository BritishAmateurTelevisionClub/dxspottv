<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>ATV Repeaters</title>
</head>
<body>
<h1>Repeaters</h1>
<?php
require_once('dxspottv_pdo.php');

                                repeater_input_freqs.forEach(function(slot_name) {
                                        if(row[slot_name]!= null) outputHTML += 'Input: <span itemprop="input frequency">'+row[slot_name]+'</span>MHz<br>'
                                });
                                repeater_output_freqs.forEach(function(slot_name) {
                                        if(row[slot_name]!= null) outputHTML += 'Output: <span itemprop="output frequency">'+row[slot_name]+'</span>MHz<br>'
                                });
                                repeater_bands.forEach(function(band_name) {
                                        if(row[band_name]== 1) outputHTML += 'Band: <span itemprop="band">'+band_name+'</span><br>';
                                });
                                if(row['active']==1) outputHTML += 'Active: <span itemprop="active">true</span><br>';
                                outputHTML += '</div>';

$data_stmt = $dbc->prepare("SELECT * FROM all_repeaters;");
$data_stmt->execute();
$data_stmt->bindColumn(1, $callsign);
$data_stmt->bindColumn(2, $lat);
$data_stmt->bindColumn(3, $lon);
$data_stmt->bindColumn(4, $height);
$data_stmt->bindColumn(5, $locator);
$data_stmt->bindColumn(6, $location);
$data_stmt->bindColumn(7, $description);
$data_stmt->bindColumn(8, $website);
$data_stmt->bindColumn(9, $keeper);
$data_stmt->bindColumn(10, $location);
$data_stmt->bindColumn(11, $location);

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
