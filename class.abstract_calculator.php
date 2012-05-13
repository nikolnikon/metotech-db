<?php

/**
 * Абстрактный класс, который является родителем для калькуляторов
 * @author nikolnikon
 * @created 07-04-2012 16:28:05
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
	 * Содержит информацию о результате вычислений
	 * @var boolean $_success
	 */
	protected $_success;
	/**
	 * Содержит код ошибки, которая возникла в результате расчета
	 * @var integer $_errorCode
	 */
	protected $_errorCode;
	/**
	 * Содержит сообщение об ошибке, которая возникла в результате расчета
	 * @var string $_errorMsg
	 */
	protected $_errorMsg;
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
	 * @param boolean $need_handle_result Говорит, надо ли дополнительно обрабатывать результаты вычислений
	 * @return boolean Возвращает успех или неудачу
	 */
	public function calc($need_handle_result = true) {
		$this->_success = call_user_func_array($this->_calcFuncName, array($this->_parameters, &$this->_result));
		$this->_errorCode = CALCERROR;
		if ($need_handle_result) {
			$this->_success = $this->_handleCalc();	
		}
		//echo '<br><br> result: '; print_r($result); echo '<br><br>';
	}
	
	/**
	 * Возвращает результаты вычислений в виде json-строки
	 * @return sting Результат вычислений в виде json-строки
	 */
	public abstract function getJSONResult();

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

/**
 * Содержит коды ошибок, возникающих в калькуляторе нагревателей
 * @author nikolnikon
 *
 */
class HeaterCalculatorErrors
{
	const DBERROR = 1;
	const DATAERROR = 2;
	const CALCERROR = 3;
}
?>
