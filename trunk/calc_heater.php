<?php
	require_once 'class.heater_calculator.php';

	$form_params = array();
	$form_params['power'] = $_POST[''];
	$form_params['voltage'] = $_POST[''];
	$form_params['pgrid'] = $_POST['pgrid'];
	$form_params['material'] = $_POST['material'];
	$form_params['temp_heating'] = $_POST['temp_heating'];
	$form_params['temp_heater'] = $_POST['temp_heater'];
	$form_params['placement'] = $_POST['placement'];
	
	$hcalc = new HeaterCalculator($form_parameters, "calc_heater");
	$hcalc->calc($parameters, $result);
	// отправка результатов клиенту
?>