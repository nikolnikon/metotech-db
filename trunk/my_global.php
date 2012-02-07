<?php

require_once 'class.mysql_dbase.php';
require_once 'class.generic_object_collection.php';
require_once 'class.alloy.php';
require_once 'class.rad_eff_coef.php';

/**
 * Класс задает константы для типа продукции
 * @author nikonov
 */
class ProductType
{
	const ROUNDS = 1;
	const STRIP = 2;
	const SHEET = 3;
	const PIPE = 4;
	const OTHER = 5;
}

class SimpleGenericObject extends GenericObject
{
	public function __construct($id, $table_name, $db) {
		$this->initialize($id, $table_name, $db);
	}
}

/**
 * Константы, которые определяют, что нужно вернуть в виде строки - список ключей или 
 * значений массива
 */
const VALUES = 0;
const KEYS = 1;

/**
 * Возвращает имя класса в зависимости от типа продукции
 * @param array $db_row строка из таблицы metalls.product
 * @return string имя подходящего класса
 */
function getProductGenObject($db_row) {
	$prod_type = $db_row['prod_type'];
	switch ($prod_type) {
		case ProductType::ROUNDS:
			return 'RoundsProduct';
		case ProductType::STRIP:
			return 'StripProduct';
		case ProductType::SHEET:
			return 'SheetProduct';
		case ProductType::PIPE:
			return 'PipeProduct';
		case ProductType::OTHER:
			return 'OtherProduct';
		default: 
			return 'OtherProduct';
	}
}

/**
 * Возвращает список значений массива, разделенных запятыми
 * @param array $array
 * @param int $type определяет, что нужно вернуть в виде строки - список ключей или 
 * значений массива
 * @return string $s
 */
function getCommaSeparatedList($array, $type = VALUES) {
	$s = '';
	if (empty($array)) {
		return 'NULL';
	}
	foreach ($array as $key => $value) {
		switch ($type) {
			case KEYS:
				$v = $key;
				break;
			case VALUES:
			default:
				$v = $value;
				break;
		}
		
		if (is_numeric($v)) {
			$s .= "$v,";
		}
		else {
			$s .= "'$v',"; // подумать над экранированием кавычек ' и "
		}
	}
	$s = substr($s, 0, strlen($s) - 1);
	return $s;
}

/**
 * Составляет запрос для фильтрации
 * @param string $table Имя таблицы, для которой строится запрос
 * @param array $returned_fields Имена полей, которые необходимо отобрать
 * @param array $conds key -> фильтруемое поле, value -> массив значений для поля. Если null, то условие не включается в запрос.
 * @return string SQL-запрос
 */
function getFilterQuery($table, $returned_fields, $conds=null) {
	$query = "SELECT ";
	foreach ($returned_fields as $returned_field) {
		$query .= "`$returned_field`, ";
	}
	$query = substr($query, 0, strlen($query) - 2);
	$query .= " FROM `metalls`.`$table`";
	if (! is_null($conds)) {
		$query .= " WHERE ";
		foreach ($conds as $cond => $values) {
			$s = getCommaSeparatedList($values);
			$query .= "`$cond` IN ($s) AND ";
		}
		$query = substr($query, 0, strlen($query) - 5);
	}
	echo "<br><br>query_2: $query<br><br>";
	return $query;	
}

/**
 * Заполняет массив, содержащий GenericObject 
 * @param string $table_name
 * @param string $class_name имя класса, производного от GenericObject.
 * @param array $array ссылка на массив, содержащий GenericObject
 * @param array $ids ссылка на массив, содержащий идентификаторы записей, которые надо получить из БД
 * @param string $func_name имя функции, необходимое классу GenericObjectCollection
 */
function fillGenericArray($table_name, $class_name, &$array, &$ids, $func_name = null) {
	$gen_obj_col = new GenericObjectCollection($table_name, $class_name, MySQLDBase::instance()); // последний параметр (БД) впоследствии нужно будет убрать
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

function calc_heater($params, $calc_res) {
	// Расчет силы тока и сопротивления
	$U = $params['U'];
	$P = $params['P'];
	if (! isset($U) || ! isset($P)) {
		return false;
	}
	$I = $P / $U;
	$R = $U / $I;
	
	// Расчет допустимой удельной поверхностной мощности
	$B_EF = $params['B_EF'];
	$A = $params['A'];
	if (! isset($B_EF) || ! isset($A)) {
		return false;
	}
	$B_DOP = $B_EF * $A;
	
	// Расчет удельного электрического сопротивления
	$RO_20 = $params['RO_20'];
	$K = $params['K'];
	if (! isset($RO_20) || ! isset($K)) {
		return false;
	}
	$RO_T = $RO_20 * $K;
	
	// Расчет диаметра и длины
	$exp_1 = 4 * $RO_T * pow($P, 2);
	$exp_2 = pow(M_PI, 2) * pow($U, 2) * $B_DOP;
	$D = pow(exp_1/exp2, 1/3);
	
	$exp_1 = $P * pow($U, 2);
	$exp_2 = 4 * M_PI * $RO_T * pow($B_DOP, 2);
	$L = pow($exp_1/$exp_2, 1/3);
	
	$calc_res['D'] = $D;
	$calc_res['L'] = $L;
	return true;
}

function get_materials_content($param) {
	if ($param == "material") {
		$table_name = 'alloys';
		$class_name = 'Alloy';
		$conds = array('heater' => array(1));
		$selected_fields = array('id');
	}
	elseif ($param == "placement") {
		$table_name = 'rad_eff_coef';
		$class_name = 'RadEffCoef';
		$conds = null;
		$selected_fields = array('id');
	}
	try {
		$db = MySQLDBase::instance();
	} catch (Exception $e) {
		print $e->getMessage();
		// обработка исключения
	};
	$query = getFilterQuery($table_name, $selected_fields, $conds);
	try {
		$rows = $db->select($query);
		foreach ($rows as $row) {
			$ids[] = $row[id];
		}
		echo "<br>ids: "; print_r($ids); echo "<br>";
		$options = array();
		fillGenericArray($table_name, $class_name, $options, $ids); // что будет в случае неудачи?
		//echo "<br>options: "; print_r($options); echo "<br>";
		foreach ($options as $option) {
			//echo "<br>".$option->id."<br>";
			$html_code .= "<option value=".$option->id.">";
			$html_code .= $option->__toString();
			$html_code .= "</option>\n";
		}
		print $html_code;
	} catch (Exception $e) {
		// обработка исключения
	}
}

?>