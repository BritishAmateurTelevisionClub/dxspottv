<?php
header('Content-Type: application/ld+json');
require_once('../dxspottv_pdo.php');

$output = array();

$data_stmt = $dbc->prepare("SELECT callsign,lat,lon,qth_r,qth,ngr,description,website, keeper_callsign FROM all_repeaters WHERE active = '1';");
$data_stmt->execute();

$data_stmt->bindColumn(1, $callsign);
$data_stmt->bindColumn(2, $lat);
$data_stmt->bindColumn(3, $lon);
$data_stmt->bindColumn(4, $locator);
$data_stmt->bindColumn(5, $location);
$data_stmt->bindColumn(6, $ngr);
$data_stmt->bindColumn(7, $description);
$data_stmt->bindColumn(8, $website);
$data_stmt->bindColumn(9, $keeper);

while ($data_stmt->fetch()) {
	$row = array();
	$row['@context'] = "http://schema.org";
	$row['@type'] = "Place";
	$row['identifier'] = $callsign;
	$row['name'] = $callsign;
	if($website!="")
	{
		$row['sameAs'] = $website;
	}
	if($description!="")
	{
		$row['description'] = $description;
	}
	$row_additionalProperty = array();
	$row['additionalProperty'];
	$row_geo = array();
        $row_geo['@type'] = "GeoCoordinates";
        $row_geo['latitude'] = $lat;
        $row_geo['longitude'] = $lon;
        $row_geo['alternateName'] = $locator;
        $row_geo['address'] = "NGR:".$ngr;
	$row['geo'] = $row_geo;
	$output[] = $row;
}
print json_encode($output);
?>
