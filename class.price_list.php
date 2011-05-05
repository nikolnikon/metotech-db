<?php

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
	 * @var MySQLDBase �������� �� ������ � ��
	 */
	private $_dbase;
	/**
	 * @var string �������� �����-�����
	 */
	private $_priceName;
	/**
	 * @var array �������� ����� ������� �� general_price, ������� ������������� ������� �����-�����, key -> id ������
	 */
	private $_priceItemsArray = array(); 
	/**
	 * @var array �������� ����� ������� �� alloys, ������� ������������ � �����-�����, key -> id ������
	 */
	private $_alloysArray  = array(); // �������� �������� ������� alloys
	/**
	 * @var array �������� ����� ������� �� production, ������� ������������ � �����-�����, key -> id ������
	 */
	private $_productArray  = array();
	/**
	 * @var array key -> �������� ������������ ����, value -> �������� ��� ������ �������
	 */
	private $_filterValues;
	
	public function __construct($dbase, $price_name, $conditions = null) {
		$this->_dbase = $dbase;
		$this->_priceName = $price_name;
		$this->_filterValues['alloy_name'] = array();
		$this->_filterValues['grade'] = array();
		$this->_filterValues['prod_name'] = array();
		$this->_load(null);
		$this->_fillFilterArray();
		if (isset($conditions)) {
			$this->_load($conditions);
		}
	}
	
	public function printToTable() {
		print "\n<form method=\"get\" action=\"test.htm\">";
		print $this->_createFilterHTMLCode();
		print "\n<input type=\"submit\" name=\"filter\" value=\"�����������\">";
		print "\n</form>";
		print "<br><br>";
		print("\n\n<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"ooo\">
			   	<tr align=\"center\" valign=\"middle\">
					<th width=\"50\" height=\"25\" scope=\"col\">�</th>
					<th width=\"100\" height=\"25\" scope=\"col\">��������</th>
					<th width=\"120\" height=\"25\" scope=\"col\">��� �������</th>
					<th width=\"101\" height=\"25\" scope=\"col\">�����</th>
					<th width=\"100\" height=\"25\" scope=\"col\">�������, �� </th>
					<th width=\"110\" height=\"25\" scope=\"col\">����� ���, ��</th>
					<th width=\"120\" height=\"25\" scope=\"col\">����, �/��</th>
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
	 * ��������� �� �� �����-����. ���� ����� ��������, �� ������ �����-����� �����������
	 * @param array $conditions ������, ���������� �������� ��� ����������� �����, key -> ����������� ����, value -> ��������
	 * @return bool ��������� ��������
	 */
	private function _load($conditions) {
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
			// �������� �������������� �� alloys � production, ��������������� ��������� �������
			foreach ($fields as $table => $values) {
				$query = $this->_getFilterQuery($table, $values, array(0 => 'id'));
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
			// �������� �������������� �� general_price, ��������������� ��������� �������
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
			if (empty($gprice_ids)) {
				print '<script language="JavaScript">alert("�� ���� ������ �� ������������� �������� ���������.");</script>';
			}
			$gprice_query = "AND `gprice_id` IN (".getCommaSeparatedList($gprice_ids).")";
		}
		
		// �������� ��� ����������� ������ ������ �����-����� �� ������� general_price
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
		$this->_fillArray('general_price', 'PriceItem', $this->_priceItemsArray, $ids_g);
		
		// �������� ��� ����������� ������ �� alloys � production
		$ids_a = array();
		$ids_p = array();
		foreach ($this->_priceItemsArray as $price_item) {
			if (! in_array($price_item->alloy_id, $ids_a))
				$ids_a[] = $price_item->alloy_id;
			if (! in_array($price_item->product_id, $ids_p))
				$ids_p[] = $price_item->product_id;
		}
		$this->_fillArray('alloys', 'Alloy', $this->_alloysArray, $ids_a);
		$this->_fillArray('production', 'Product', $this->_productArray, $ids_p, 'getProductGenObject');
		return true;
	}
	
	private function _fillArray($table_name, $class_name, &$array, &$ids, $func_name = null) {
		$gen_obj_col = new GenericObjectCollection($table_name, $class_name, $this->_dbase);
		if (isset($func_name)) {
			$gen_obj_col->setClassNameFunc($func_name);
		}
		if (! empty($ids)) {
			foreach ($ids as $id) {
				$gen_obj_col->addTuple($id);
			}
			$gen_obj_col->populateObjectArray();
			$array = $gen_obj_col->getPopulatedObjects();
		}	
	}
	
	private function _fillFilterArray() {
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
				if (! in_array($gen_obj->$key, $this->_filterValues[$key])) {
					$this->_filterValues[$key][] = $gen_obj->$key;
				}
			}
		}
		print_r($this->_filterValues);
	}
	
	/**
	 * ���������� ������ ��� ����������
	 * @param string $table ��� �������, ��� ������� �������� ������
	 * @param array $fields key -> ����������� ����, value -> ��������
	 */
	private function _getFilterQuery($table, $fields, $returned_fields) {
		$query = "SELECT ";
		foreach ($returned_fields as $returned_field) {
			$query .= "`$returned_field`, ";
		}
		$query = substr($query, 0, strlen($query) - 2);
		$query .= " FROM `metalls`.`$table` WHERE ";
		foreach ($fields as $field => $values) {
			$s = getCommaSeparatedList($values);
			$query .= "`$field` IN ($s) AND ";
		}
		$query = substr($query, 0, strlen($query) - 5);
		print "<br><br>query_2: $query<br><br>";
		return $query;	
	}
	
	/**
	 * ��������� ������ �������� ��� �������
	 * @param $type string ���������� ��� ������; MATERIAL - ��������, PRODUCT - �������� ���������, GRADE - ����� ������.
	 */
	private function _createFilterHTMLCode() {
		include 'tpl.multiple_select.php';
		$mtpl_sel_code = '';
		echo '<br><br>'; print_r($this->_filterValues); echo '<br><br>';
		foreach ($this->_filterValues as $field => $values) {
			$select_name = $field."[]";
			$mtpl_sel_code .= str_replace('{name}', $select_name, $header);
			$options = '';
			foreach ($values as $value) {
				$options .= str_replace(array('{value}', '{name_value}'), array($value, $value), $option);
			}
			$mtpl_sel_code .= $options.$footer;
		}
		return $mtpl_sel_code;
	}
}
?>