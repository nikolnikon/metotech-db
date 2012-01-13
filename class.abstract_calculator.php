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
	 * Имя функции, которая выполняет вычисления
	 */
	private $_calcFuncName;
	/**
	 * Содержит параметры, необходимые для вычисления значения
	 */
	private $_parameters;

	public function __construct($form_parameters, $calc_func_name) {
		$this->_loadParameters($form_parameters);
	}
	
	/**
	 * Выполняет вычисления, используя parameters и возвращает результат в result
	 * @param array $parameters Параметры, необходимые для вычислений
	 * @param array $result Результат вычислений
	 * @return boolean Возвращает успех или неудачу
	 */
	public function calc($result) {
		$success = call_user_func_array($this->_calcFuncName, array($this->_parameters));
	}

	/**
	 * Получает параметры, необходимые для вычислений
	 * @return boolean Возвращает успех или неудачу
	 */
	abstract private function _loadParameters($form_parameters);
}
?>