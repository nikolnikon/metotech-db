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
	 * Содержит результаты вычислений
	 * @var array
	 */
	protected $_result;
	/**
	 * Имя функции, которая выполняет вычисления
	 * @var array $_calcFuncName
	 */
	private $_calcFuncName;
	/**
	 * Содержит успех или неудачу загрузки данных
	 * @var boolean $_loaded
	 */
	private $_loaded = false;

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
	public function calc($need_handle_result = true) {
		$success = call_user_func_array($this->_calcFuncName, array($this->_parameters, &$this->_result));
		if ($need_handle_result) {
			$this->_handleCalc();	
		}
		//echo '<br><br> result: '; print_r($result); echo '<br><br>';
	}
	
	/**
	 * Возвращает результаты вычислений в виде json-строки
	 * @return sting Результат вычислений в виде json-строки
	 */
	public function getJSONResult() {
		$jsn = json_encode($this->_result);
		if (json_last_error() == JSON_ERROR_NONE) {
			return $jsn;
		}
		else {
			// обработать ошибку кодирования в json
		}
		return $jsn;
	}

	/**
	 * Загружает параметры, необходимые для вычислений
	 * @param array $form_parameters Данные, полученные от формы
	 * @return boolean Возвращает успех или неудачу
	 */
	protected abstract function _loadParameters($form_parameters);
	
	/**
	 * Выполняет необходимые действия после выполнения расчетов
	 */
	protected abstract function _handleCalc();
}
?>