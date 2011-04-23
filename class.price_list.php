<?php

require_once('class.mysql_dbase.php');
require_once('class.generic_object_collection.php');
require_once('class.price_item.php');
require_once('class.alloy.php');
require_once('class.product.php');
require_once('class.other_product.php');
require_once('class.sheet_product.php');
require_once('my_global.php');

class PriceList
{
	private $dbase;
	private $priceName;
	/**
	 * @var array содержит набор записей из general_price, которые удовлетворяют данному прайс-листу, key -> id записи
	 */
	private $_priceItemsArray = array(); 
	/**
	 * @var array содержит набор записей из alloys, которые используются в прайс-листе, key -> id записи
	 */
	private $_alloysArray  = array(); // содержит фрагмент таблицы alloys
	/**
	 * @var array содержит набор записей из production, которые используются в прайс-листе, key -> id записи
	 */
	private $_productArray  = array();
	
	public function __construct($dbase, $price_name) {
		$this->dbase = $dbase;
		$this->priceName = $price_name;
		
		// получаем все необходимые записи общего прайс-листа из таблицы general_price
		$query_1 = "SELECT `id` FROM `metalls`.`special_prices` WHERE `price_name` = '".mysql_real_escape_string($this->priceName)."'";
		$query_2 = "SELECT `gprice_id` FROM `metalls`.`prices_mapping` WHERE `sprice_id` = ($query_1)";
		
		try {
			$ids = $this->dbase->select($query_2);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
		
		echo "ids: ";
		print_r($ids);
		echo '<br>';
		
		$gen_obj_col = new GenericObjectCollection('general_price', 'PriceItem', $this->dbase);
		foreach ($ids as $row) {
			$gen_obj_col->addTuple($row["gprice_id"]);
		}
		$gen_obj_col->populateObjectArray();
		$this->_priceItemsArray = $gen_obj_col->getPopulatedObjects();
		unset($gen_obj_col);
		
		// получаем все необходимые записи из alloys и production
		$ids_a = array();
		$ids_p = array();
		foreach ($this->_priceItemsArray as $price_item) {
			if (! in_array($price_item->alloy_id, $ids_a))
				$ids_a[] = $price_item->alloy_id;
			if (! in_array($price_item->product_id, $ids_p))
				$ids_p[] = $price_item->product_id;
		}
		unset($gen_obj_col);

		$gen_obj_col = new GenericObjectCollection('alloys', 'Alloy', $this->dbase);
		foreach ($ids_a as $id) {
			$gen_obj_col->addTuple($id);
		}
		$gen_obj_col->populateObjectArray();
		$this->_alloysArray = $gen_obj_col->getPopulatedObjects();
		unset($gen_obj_col);
		
		$gen_obj_col = new GenericObjectCollection('production', 'Product', $this->dbase);
		$gen_obj_col->setClassNameFunc('getProductGenObject');
		foreach ($ids_p as $id) {
			$gen_obj_col->addTuple($id);
		}
		$gen_obj_col->populateObjectArray();
		$this->_productArray = $gen_obj_col->getPopulatedObjects();
	}
	
	public function printToTable() {
		print("\n\n<table border=\"1\" cellspacing=\"1\" cellpadding=\"1\" class=\"ooo\">
			   	<tr align=\"center\" valign=\"middle\">
					<th width=\"50\" height=\"25\" scope=\"col\">№</th>
					<th width=\"100\" height=\"25\" scope=\"col\">Материал</th>
					<th width=\"120\" height=\"25\" scope=\"col\">Тип проката</th>
					<th width=\"101\" height=\"25\" scope=\"col\">Марка</th>
					<th width=\"100\" height=\"25\" scope=\"col\">Размеры, мм </th>
					<th width=\"110\" height=\"25\" scope=\"col\">Общий вес, кг</th>
					<th width=\"120\" height=\"25\" scope=\"col\">Цена, р/кг</th>
				</tr>");
		print("<tr>\n");
		$count = 1;
		foreach ($this->_priceItemsArray as $price_item) {
			/*$alloy = $this->_alloysArray[$price_item->alloy_id];
			$product = $this->_productArray[$price_item->product_id];
			
			echo "<br>$count";
			echo "<br>alloy_name: $alloy->name";
			echo "<br>product_name: $product->prod_name";
			echo "<br>grade: $alloy->grade";
			echo "<br>size: $product";
			echo "<br>mass: $price_item->mass";
			echo "<br>price: $price_item->price";*/
			
			$str .= "\n\t<tr>\n";
			$alloy = $this->_alloysArray[$price_item->alloy_id];
			$product = $this->_productArray[$price_item->product_id];
			$str .= "\t\t<td>$count</td>\n";
			$str .= "\t\t<td>$alloy->name</td>\n";
			$str .= "\t\t<td>$product->prod_name</td>\n";
			$str .= "\t\t<td>$alloy->grade</td>\n";
			$str .= "\t\t<td>$product</td>\n";
			$str .= "\t\t<td>$price_item->mass</td>\n";
			$str .= "\t\t<td>$price_item->price</td>\n";
			$str .= "\t</tr>\n";
			$count += 1;
		}
		print $str;
		print("</table>");
	}
}

?>