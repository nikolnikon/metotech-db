<?php

require_once('mysql_dbase.php');

class PriceList
{
	private $dbase;
	private $priceName;
	private $generalPrice; // �������� �������� ������� general_price
	private $alloys; // �������� �������� ������� alloys
	private $production; // �������� �������� ������� production
	private $mapAlloysIdRow; // �������� ������������ alloy_id ������ � �������
	private $mapProductIdRow; // �������� ������������ product_id ������ � �������
	
	public function __construct($dbase, $price_name) {
		$this->dbase = $dbase;
		$this->priceName = $price_name;
		
		//$db_name = $this->dbase->getDBName();
		
		// �������� id ���� ����������� ������� ������ �����-����� �� ������� general_price
		$query_1 = "SELECT `id` FROM `metalls`.`special_prices` WHERE `price_name` = '".mysql_real_escape_string($this->priceName)."'";
		$query_2 = "SELECT `gprice_id` FROM `metalls`.`prices_mapping` WHERE `sprice_id` = ($query_1)";
		
		print($query_2."<br><br>");
		
		try {
			$ids = $this->dbase->select($query_2);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
		foreach($ids as $row) {
			print($row["gprice_id"]."	");
		}
		print("<br><br>");
		// �������� ��� ����������� ������ ������ �����-����� �� ������� general_price
		$query_1 = "SELECT * FROM `metalls`.`general_price` WHERE ";
		foreach ($ids as $row) {
			$query_1 .= "`id` = ".mysql_real_escape_string($row["gprice_id"])." OR "; 	 
		}
		$query_1 = substr($query_1, 0, -4);
		print($query_1."<br><br>");
		unset($ids);
		
		try {
			$this->generalPrice = $this->dbase->select($query_1);
		}
		catch (Exception $e) {
			echo $e->getMessage();
		}
		// �������� ��� ����������� ������ �� alloys � production
		$ids_a = array();
		$ids_p = array();
		foreach ($this->generalPrice as $row) {
			if (! in_array($row["alloy_id"], $ids_a))
				$ids_a[] = $row["alloy_id"];
			if (! in_array($row["product_id"], $ids_p))
				$ids_p[] = $row["product_id"];
		}

		$query_1 = "SELECT `id`, `name`, `grade` FROM `metalls`.`alloys` WHERE ";
		foreach ($ids_a as $id) {
			$query_1 .= "`id` = ".mysql_real_escape_string($id)." OR ";
		}
		$query_1 = substr($query_1, 0, -4);
		
		$query_2 = "SELECT * FROM `metalls`.`production` WHERE ";
		foreach ($ids_p as $id) {
			$query_2 .= "`id` = ".mysql_real_escape_string($id)." OR ";
		}
		$query_2 = substr($query_2, 0, -4);
		
		try {
			$this->alloys = $this->dbase->select($query_1);
			$this->production = $this->dbase->select($query_2);
		}
		catch (Exception $e) {
			echo "��������� ���������� �������".NL;
			echo $e->getMessage();
		}
		
		// ��������� ������� ������������
		foreach ($this->alloys as $key => $rec) {
			$this->mapAlloysIdRow[$rec["id"]] = $key;
		}
		foreach ($this->production as $key => $rec) {
			$this->mapProductIdRowIdRow[$rec["id"]] = $key;
		}
	}
}

?>