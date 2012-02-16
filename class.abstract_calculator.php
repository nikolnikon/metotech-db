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
	 * Содержит параметры, необходимые для вычисления значения
	 * @var array $_parameters
	 */
	protected $_parameters;
	/**
	 * Имя функции, которая выполняет вычисления
	 * @var array $_calcFuncName
	 */
	private $_calcFuncName;
	/**
	 * Содержит успех или неудачу загрузки данных
	 * @var boolean $_loaded
	 */
	private $_loaded;

	public function __construct($form_parameters, $calc_func_name) {
		$this->_loadParameters($form_parameters);
		$this->_calcFuncName = $calc_func_name;
	}
	
	/**
	 * Выполняет вычисления, используя parameters и возвращает результат в result
	 * @param array $parameters Параметры, необходимые для вычислений
	 * @param array $result Результат вычислений
	 * @return boolean Возвращает успех или неудачу
	 */
	public function calc(&$result) {
		$success = call_user_func_array($this->_calcFuncName, array($this->_parameters, &$result));
		//echo '<br><br> result: '; print_r($result); echo '<br><br>';
	}

	/**
	 * Загружает параметры, необходимые для вычислений
	 * @param array $form_parameters Данные, полученные от формы
	 * @return boolean Возвращает успех или неудачу
	 */
	protected abstract function _loadParameters($form_parameters);
}
?>