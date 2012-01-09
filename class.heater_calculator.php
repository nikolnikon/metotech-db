<?php
require_once ('class.abstract_calculator.php');

/**
 * Калькулятор нагревателей
 * @author nikolnikon
 * @version 1.0
 * @created 07-янв-2012 16:28:21
 */
class HeaterCalculator extends AbstractCalculator
{

	function HeaterCalculator()
	{
	}



	/**
	 * Получает параметры, необходимые для вычислений
	 */
	function _loadParameters()
	{
	}

	/**
	 * Выполняет вычисления, используя parameters и возвращает результат в reult
	 * 
	 * @param parameters    Параметры, необходимые для вычислений
	 * @param result
	 */
	function calc($parameters, $result)
	{
	}

}
?>