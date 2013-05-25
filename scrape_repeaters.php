<?php
include_once 'simple_html_dom.php';

$html = file_get_html("http://www.ukrepeater.net/repeaterlist5.htm");

$table = $html->find(table.mixed, 0);

foreach($table->find('tr') as $row) {
	foreach($row->find('td.class=minil') as $cell) {
		print "<pre>" + $cell->plaintext + "</pre><br>";
	}
}
?>
