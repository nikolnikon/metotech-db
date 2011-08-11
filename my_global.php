<?php

/**
 * Класс задает константы для типа продукции
 * @author nikonov
 *
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
 * @return string $s
 */
function getCommaSeparatedList($array) {
	$s = '';
	if (empty($array)) {
		return 'NULL';
	}
	foreach ($array as $value) {
		if (is_numeric($value)) {
			$s .= "$value,";
		}
		else {
			$s .= "'$value',";
		}
	}
	$s = substr($s, 0, strlen($s) - 1);
	return $s;
}

?>