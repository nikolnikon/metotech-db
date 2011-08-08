<?php
	header('Content-Type: text/xlm');
	$text = file_get_contents('test.xml');
	echo $text;
?>