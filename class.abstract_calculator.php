<?php

/**
 * Абстрактный класс, который является родителем для калькуляторов
 * @author nikolnikon
 * @version 1.0
 * @created 07-���-2012 16:28:05
 */
abstract class AbstractCalculator
{
	/**
	 * Функция, которая выполняет вычисления
	 */
	var $_calcFunc;
	/**
	 * Содержит параметры, необходимые для вычисления значения
	 */
	var $_parameters;

	public function __construct()
	{
		$this->_loadParameters();
	}
	
	/**
	 * Выполняет вычисления, используя parameters и возвращает результат в result
	 * @param array $parameters Параметры, необходимые для вычислений
	 * @param array $result Результат вычислений
	 */
	public function calc($parameters, $result)
	{
		$success = call_user_func_array($this->_calcFunc, $this->_parameters);
	}

	/**
	 * Получает параметры, необходимые для вычислений
	 * @return boolean Возвращает
	 */
	private function _loadParameters()
	{
	}
}
?>