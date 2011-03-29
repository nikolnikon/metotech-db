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
		
		$query_1 = "SELECT `id` FROM `metalls`.`special_prices` WHERE `price_name` = $this->priceList";
		$query_2 = "SELECT `gprice_id` FROM `metalls`.`prices_mapping` WHERE `sprice_id` = ($query_1)";
		
		// получаем id всех необходимых записей общего прайс-листа из таблицы general_price 
		try {
			$gpr_ids = $this->dbase->select($query_2);
		}
		catch(Exception $e) {
			echo "Неудачное выполнение запроса".NL;
			echo $e->getMessage();
		}
		
		$query_1 = "SELECT * FROM `metalls`.`general_price` WHERE ";
		foreach ($gpr_ids as $id) {
			$query_1 .= "`id` = ".$id." OR "; 	 
		}
		$query_1 = substr($query_1, 0, -4);
		
		try {
			$general_price = $this->dbase->select($query_1);
		}
		catch (Exception $e) {
			echo "Неудачное выполнение запроса".NL;
			echo $e->getMessage();
		}
		
		
	}
}

?>