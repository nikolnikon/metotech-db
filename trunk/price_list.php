<?php

require_once('mysql_dbase.inc');

class PriceList
{
	private $dbase;
	private $priceList;
	
	public function __construct($dbase, $price_name) {
		$this->dbase = $dbase;
		$this->priceList = $price_name;
		
		$db_name = $this->dbase->getDBName();
		
		// получаем id всех необходимых записей общего прайс-листа из таблицы general_price
		$query_1 = "SELECT `id` FROM `metalls`.`special_prices` WHERE `price_name` = $this->priceList";
		$query_2 = "SELECT `gprice_id` FROM `metalls`.`prices_mapping` WHERE `sprice_id` = ($query_1)";
		
		try {
			$ids = $this->dbase->select($query_2);
		}
		catch(Exception $e) {
			echo "Неудачное выполнение запроса".NL;
			echo $e->getMessage();
		}
		// получаем все необходимые записи общего прайс-листа из таблицы general_price
		$query_1 = "SELECT * FROM `metalls`.`general_price` WHERE ";
		foreach ($ids as $id) {
			$query_1 .= "`id` = ".$id." OR "; 	 
		}
		$query_1 = substr($query_1, 0, -4);
		unset($ids);
		
		try {
			$general_price = $this->dbase->select($query_1);
		}
		catch (Exception $e) {
			echo "Неудачное выполнение запроса".NL;
			echo $e->getMessage();
		}
		// получаем все необходимые записи из alloys и production
		$ids_a = array();
		$ids_p = array();
		foreach ($general_price as $row) {
			if (! in_array($row["alloy_id"], $ids_a))
				$ids_a[] = $row["alloy_id"];
			if (! in_array($row["product_id"], $ids_p))
				$ids_p[] = $row["product_id"];
		}

		$query_1 = "SELECT `id`, `name`, `grade` FROM `metalls`.`alloys` WHERE ";
		foreach ($ids_a as $id) {
			$query_1 .= "`id` = ".$id." OR ";
		}
		$query_1 = substr($query_1, 0, -4);
		
		$query_2 = "SELECT * FROM `metalls`.`production` WHERE ";
		foreach ($ids_p as $id) {
			$query_2 .= "`id` = ".$id." OR ";
		}
		$query_2 = substr($query_2, 0, -4);
		
		try {
			$alloys = $this->dbase->select($query_1);
			$production = $this->dbase->select($query_2);
		}
		catch (Exception $e) {
			echo "Неудачное выполнение запроса".NL;
			echo $e->getMessage();
		}
	}
}

?>