<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
<?php
	require_once('class.price_list.php');
	try {
		$db = new MySQLDBase('127.0.0.1', 'root', '', 'metalls');
	}
	catch(Exception $e) {
		echo "Ошибка\n".$e->getMessage();
	}
	$price = new PriceList($db, "nikel-price");
	//$result = $price->insertItem($_GET);
	//$result = $price->updateItem(6, $_GET);
	$result = $price->removeItem(6);
	echo $result;
?>
</body>
</html>