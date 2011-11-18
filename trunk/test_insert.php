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
	/*$array['alloy_name'] = "Никель";
	$array['grade'] = "НПА1";
	$array['prod_name'] = "Проволока";
	$array['diameter'] = 1.2;
	$array['mass'] = 100;
	$array['price'] = 1500;*/
	$result = $price->insertItem($_GET);
	echo $result;
?>
</body>
</html>