﻿<?php

require_once('class.mysql_dbase.php');
require_once('class.generic_object_collection.php');
require_once('class.price_item.php');
require_once('class.alloy.php');
require_once('class.product.php');
require_once('class.other_product.php');
require_once('class.sheet_product.php');
require_once('class.strip_product.php');
require_once('class.rounds_product.php');
require_once('my_global.php');

class PriceList
{
	/**
	 * @var MySQLDBase отвечает за работу с БД
	 */
	private $_dbase;
	/**
	 * @var string название прайс-листа
	 */
	private $_priceName;
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
	
	private $_filterValues;
	
	public function __construct($dbase, $price_name, $conditions = null) {
		$this->_dbase = $dbase;
		$this->_priceName = $price_name;
		$this->_load($conditions);
		$this->_filterValues['alloy_name'] = array();
		$this->_filterValues['grade'] = array();
		$this->_filterValues['prod_name'] = array();
	}
	
	public function printToTable() {
		print "\n<form method=\"get\" action=\"test.htm\">";
		print ($this->_createMultipleSelectCode('MATERIAL'));
		print ($this->_createMultipleSelectCode('PRODUCT'));
		print ($this->_createMultipleSelectCode('GRADE'));
		print "\n<input type=\"submit\" name=\"filter\" value=\"Фильтровать\">";
		print "\n</form>";
		print "<br><br>";
		print("\n\n<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"ooo\">
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
			$str .= "\n\t<tr>\n";
			$alloy = $this->_alloysArray[$price_item->alloy_id];
			$product = $this->_productArray[$price_item->product_id];
			$str .= "\t\t<td>$count</td>\n";
			$str .= "\t\t<td>$alloy->alloy_name</td>\n";
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
	
	/**
	 * Загружает из БД прайс-лист. Если задан параметр, то записи прайс-листа фильтруются
	 * @param array $conditions массив, содержащий значения для фильтруемых полей, key -> фильтруемое поле, value -> значения
	 * @return bool результат загрузки
	 */
	private function _load(&$conditions) {
		$gprice_query = '';
		if (! is_null($conditions) && is_array($conditions)) {
			$fields = array();
			if (array_key_exists('alloy_name', $conditions)) {
				$fields['alloys']['alloy_name'] = $conditions['alloy_name'];
			}
			if (array_key_exists('grade', $conditions)) {
				$fields['alloys']['grade']  = $conditions['grade'];
			}
			if (array_key_exists('prod_name', $conditions)) {
				$fields['production']['prod_name']  = $conditions['prod_name'];
			}
			
			$ids_list = array();
			// получаем идентификаторы из alloys и production, удовлетворяющие критериям фильтра
			foreach ($fields as $table => $values) {
				$query = $this->_getFilterQuery($table, $values);
				try {
					$ids = $this->_dbase->select($query);
					foreach ($ids as $row) {
						$ids_list[$table][] = $row['id'];
					}
				}
				catch (Exception $e) {
					print $e->getMessage();
					return false;
				}
			}
			// получаем идентификаторы из general_price, удовлетворяющие критериям фильтра
			$gprice_query = "SELECT `id` FROM `metalls`.`general_price` WHERE ";
			$keys = array_keys($ids_list);
			foreach ($keys as $key) {
				if ($key == 'alloys') {
					$field = 'alloy_id';
				}
				if ($key == 'production') {
					$field = 'product_id';	
				}
				$gprice_query .= "`$field` IN (";
				$s = getCommaSeparatedList($ids_list[$key]);
				$gprice_query .= "$s) AND ";
			}
			$gprice_query = substr($gprice_query, 0, strlen($gprice_query) - 5);
			try {
				$ids = $this->_dbase->select($gprice_query);
				foreach ($ids as $row) {
					$gprice_ids[] = $row['id'];
				}
			}
			catch (Exception $e) {
				print $e->getMessage();
				return false;
			}
			$gprice_query = "AND `gprice_id` IN (".getCommaSeparatedList($gprice_ids).")";
		}
		
		// получаем все необходимые записи общего прайс-листа из таблицы general_price
		$query_1 = "SELECT `id` FROM `metalls`.`special_prices` WHERE `price_name` = '".mysql_real_escape_string($this->_priceName)."'";
		$query_2 = "SELECT `gprice_id` FROM `metalls`.`prices_mapping` WHERE `sprice_id` = ($query_1) $gprice_query";
		
		echo "<br><br>query_2: $query_2<br><br>";
		
		try {
			$ids = $this->_dbase->select($query_2);
		}
		catch(Exception $e) {
			echo $e->getMessage();
			return false;
		}
		
		foreach ($ids as $row) {
			$ids_g[] = $row["gprice_id"];
		}
		$this->_fillArray('general_price', 'PriceItem', '_priceItemsArray', $ids_g);
		
		// получаем все необходимые записи из alloys и production
		$ids_a = array();
		$ids_p = array();
		foreach ($this->_priceItemsArray as $price_item) {
			if (! in_array($price_item->alloy_id, $ids_a))
				$ids_a[] = $price_item->alloy_id;
			if (! in_array($price_item->product_id, $ids_p))
				$ids_p[] = $price_item->product_id;
		}
		$this->_fillArray('alloys', 'Alloy', '_alloysArray', $ids_a);
		$this->_fillArray('production', 'Product', '_productArray', $ids_p, 'getProductGenObject');
		return true;
	}
	
	private function _fillArray($table_name, $class_name, $array_name, &$ids, $func_name = null) {
		$gen_obj_col = new GenericObjectCollection($table_name, $class_name, $this->_dbase);
		if (isset($func_name)) {
			$gen_obj_col->setClassNameFunc($func_name);
		}
		foreach ($ids as $id) {
			$gen_obj_col->addTuple($id);
		}
		$gen_obj_col->populateObjectArray();
		$this->$array_name = $gen_obj_col->getPopulatedObjects();	
	}
	
	private function _fillFilterArray(&$conditions) {
		if (! is_null($conditions) && is_array($conditions)) {
			$query_1 = "SELECT `id` FROM `metalls`.`special_prices` WHERE `price_name` = '".mysql_real_escape_string($this->_priceName)."'";
			$query_2 = "SELECT `gprice_id` FROM `metalls`.`prices_mapping` WHERE `sprice_id` = ($query_1) $gprice_query";
			$query = "SELECT `alloy_id`, `product_id` FROM `metalls`.`general_price` WHERE id IN (";
			$query = "SELECT `alloy_name`, `grade`, `product_name` FROM `metalls`.`alloys`, `metalls`."
		}
		else {
			$keys = array_keys($this->_filterValues);
			foreach ($keys as $key) {
				switch ($key) {
					case 'alloy_name':
					case 'grade':
						$array = $this->_alloysArray;
						break;
					case 'prod_name':
						$array = $this->_productArray;
						break;
				}
				foreach ($array as $gen_obj) {
					$this->_filterValues[$key][] = $gen_obj->$key;
				}
			}
		}
	}
	
	/**
	 * Составляет запрос для фильтрации
	 * @param string $table имя таблицы, для которой строится запрос
	 * @param array $fields key -> фильтруемое поле, value -> значения
	 */
	private function _getFilterQuery($table, $fields) {
		$query .= "SELECT `id` FROM `metalls`.`$table` WHERE ";
		print_r($fields);
		foreach ($fields as $field => $values) {
			$s = getCommaSeparatedList($values);
			$query .= "`$field` IN ($s) AND ";
		}
		$query = substr($query, 0, strlen($query) - 5);
		print "<br><br>query_2: $query<br><br>";
		return $query;	
	}
	
	/**
	 * Формирует список значений для фильтра
	 * @param $type string определяет тип списка; MATERIAL - материал, PRODUCT - название продукции, GRADE - марка сплава.
	 */
	private function _createMultipleSelectCode($type) {
		$array = array();
		$field = '';
		$select_name = '';
		switch ($type) {
			case 'MATERIAL':
				$array = $this->_alloysArray;
				$field = 'alloy_name';
				break;
			case 'GRADE':
				$array = $this->_alloysArray;
				$field = 'grade';
				break;
			case 'PRODUCT':
				$array = $this->_productArray;
				$field = 'prod_name';
				break;
		}
		$return_array = array();
		foreach ($array as $gen_obj) {
			$value = $gen_obj->$field;
			if (! in_array($value, $return_array)) {
				$return_array[] = $value;
			}
		}
		
		include 'tpl.multiple_select.php';
		$select_name = $field."[]";
		$mtpl_sel_code = str_replace('{name}', $select_name, $header);
		$options = '';
		foreach ($return_array as $key => $value) {
			$options .= str_replace(array('{value}', '{name_value}'), array($value, $value), $option);
		}
		$mtpl_sel_code .= $options.$footer;
		return $mtpl_sel_code;
	}
}
?>