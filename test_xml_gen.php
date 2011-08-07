<?php
	require_once 'class.price_list.php';
	
	try {
		$db = new MySQLDBase('127.0.0.1', 'root', '', 'metalls');
	}
	catch(Exception $e) {
		echo "Ошибка\n".$e->getMessage();
	}
	$price = new PriceList($db, "nikel-price", $_GET);
	$price->getXML();
?>