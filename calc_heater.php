<?php
	require_once 'class.heater_calculator.php';

	$form_params = array();
	$form_params['power'] = $_GET['power'];
	$form_params['voltage'] = $_GET['voltage'];
	$form_params['pgrid'] = $_GET['pgrid'];
	$form_params['material'] = $_GET['material'];
	$form_params['max_temp'] = $_GET['max_temp'];
	$form_params['resistivity'] = $_GET['resistivity'];
	$form_params['temp_heating'] = $_GET['temp_heating'];
	$form_params['placement'] = $_GET['placement'];
	
	$hcalc = new HeaterCalculator($form_parameters, "calc_heater");
	$hcalc->calc($result);
	// отправка результатов клиенту
?>