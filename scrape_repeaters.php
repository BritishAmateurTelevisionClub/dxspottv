<?php
include_once 'simple_html_dom.php';

$html = file_get_html("http://www.ukrepeater.net/repeaterlist5.htm");
print "HTML: " . $html;
foreach($html->find('table.mixed tr') as $row) {
	print "Row: " . $row;
	foreach($row->find('td.minil') as $cell) {
		print $cell;
		print "<pre>" . $cell->plaintext . "</pre><br>";
	}
}
?>
