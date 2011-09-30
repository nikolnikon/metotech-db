<?php

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
				$v = $values;
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

?>