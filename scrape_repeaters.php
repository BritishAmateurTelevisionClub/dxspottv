<?php
include_once 'simple_html_dom.php';

$html = file_get_html("http://www.ukrepeater.net/repeaterlist5.htm");

foreach($html->find('table.mix tr') as $row) {
	foreach($row->find('td.minil') as $cell) {
		print "<pre>" . $cell->plaintext . "</pre><br>";
	}
}
?>
