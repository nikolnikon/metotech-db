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
	//echo "<br><br>query_2: $query<br><br>";
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

function calc_heater($params, &$calc_res) {
	// Расчет силы тока и сопротивления
	$U = $params['U'];
	$P = $params['P'];
	if (! isset($U) || ! isset($P)) {
		return false;
	}
	$I = $P / $U;
	$R = $U / $I;
	
	// Расчет удельного электрического сопротивления
	$RO_20 = $params['RO_20'] * pow (10, -6);
	$K = $params['K'];
	if (! isset($RO_20) || ! isset($K)) {
		return false;
	}
	$RO_T = $RO_20 * $K;
	
	// Расчет допустимой удельной поверхностной мощности
	if (! isset($params['D'])) {
		$B_EF = $params['B_EF'] * pow(10, 4);
		$A = $params['A'];
		if (! isset($B_EF) || ! isset($A)) {
			return false;
		}
		$B_DOP = $B_EF * $A;
	}
	else {
		$exp_1 = 4 * $RO_T * pow($P, 2);
		$exp_2 = pow($params['D'], 3) * pow(M_PI, 2) * pow($U, 2);
		$B_DOP = $exp_1 / $exp_2;
	}
	
	// Расчет диаметра
	if (! isset($params['D'])) {
		$exp_1 = 4 * $RO_T * pow($P, 2);
		//echo '<br><br> exp_1: '.$exp_1.'<br><br>';
		$exp_2 = pow(M_PI, 2) * pow($U, 2) * $B_DOP;
		//echo '<br><br> exp_2: '.$exp_2.'<br><br>';
		$D = pow($exp_1/$exp_2, 1/3);
	}
	else {
		$D = $params['D'];
	}
	
	// Расчет длины
	$exp_1 = $P * pow($U, 2);
	$exp_2 = 4 * M_PI * $RO_T * pow($B_DOP, 2);
	$L = pow($exp_1/$exp_2, 1/3);
	
	$calc_res['D'] = $D;
	$calc_res['L'] = $L;
	return true;
}

function get_heater_form_content($param) {
	/*header("HTTP/1.0 500 Internal Server Error", true, 500);
	print json_encode(array("status"=>"error", "message"=>"it's very bad!"));*/
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
		print json_encode(array("status"=>"db_error", "error_header"=>"Ошибка БД", "error_message"=>"Запрос к БД не может быть выполнен. Повторите попытку расчета позже"));
		// обработка исключения
	};
	$query = getFilterQuery($table_name, $selected_fields, $conds);
	try {
		$rows = $db->select($query);
		foreach ($rows as $row) {
			$ids[] = $row[id];
		}
		//echo "<br>ids: "; print_r($ids); echo "<br>";
		$options = array();
		fillGenericArray($table_name, $class_name, $options, $ids); // что будет в случае неудачи?
		//echo "<br>options: "; print_r($options); echo "<br>";
		foreach ($options as $option) {
			if ($param == "material") {
				// получаем максимальную рабочую температуру нагревателя (наибольшее значение)
				$query = "SELECT MAX(`max_temp`) FROM `max_heater_temp` WHERE `alloy_id` = ".mysql_real_escape_string($option->id);
				$res = $db->select($query);
				//echo "res: "; print_r($res); echo "\n";
				if (empty($res[0]['MAX(`max_temp`)'])) { // если в БД нет максимальной температуры для данного сплава, то не включаем его в список доступных сплавов
					continue;
				}
				$max_heater_temp = $res[0]['MAX(`max_temp`)'];
				// получаем допустимые значения температуры нагревателя 
				$query = "SELECT DISTINCT `temp_heater` FROM `metalls`.`heater_surface_power` WHERE `temp_heater` <= ".mysql_real_escape_string($max_heater_temp);
				$temps = $db->select($query);
				foreach ($temps as $temp) {
					$arr[] = $temp['temp_heater'];
				}
				$t_h = getCommaSeparatedList($arr);
				// получаем допустимые значения температуры изделия
				$query = "SELECT DISTINCT `temp_solid` FROM `metalls`.`heater_surface_power` WHERE `temp_solid` < ".mysql_real_escape_string($max_heater_temp);
				$temps = $db->select($query);
				unset($arr);
				foreach ($temps as $temp) {
					$arr[] = $temp['temp_solid'];
				}
				$t_s = getCommaSeparatedList($arr);
				//echo "<br>t_s: ".$t_s."<br>";
				
				$html_code .= "<option value=\"".$option->id."\" data-max_temp=\"".$option->max_heater_temp."\" data-resistivity=\"".$option->resistivity."\" data-htemps=\"".$t_h."\" data-stemps=\"".$t_s."\" data-density=\"".$option->density."\">";
				$html_code .= $option->__toString();
				$html_code .= "</option>\n";
				unset($arr);
			}
			elseif ($param == "placement") {
				$av_coef = ($option->min_coef + $option->max_coef) / 2;
				$html_code .= "<option value=\"".$av_coef."\">";
				$html_code .= $option->__toString();
				$html_code .= "</option>\n";
			}
		}
		print $html_code;
	} catch (Exception $e) {
		print json_encode(array("status"=>"db_error", "error_header"=>"Ошибка БД", "error_message"=>"Запрос к БД не может быть выполнен. Повторите попытку расчета позже"));
	}
}
?>
