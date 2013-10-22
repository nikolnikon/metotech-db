<?php
	require_once 'class.heater_calculator.php';
	require_once 'my_global.php';

	$form_params = array();
	$form_params['power'] = $_GET['power'];
	$form_params['voltage'] = $_GET['voltage'];
	$form_params['pgrid'] = $_GET['pgrid'];
	$form_params['material'] = $_GET['material'];
	$form_params['resistivity'] = $_GET['resistivity'];
	$form_params['density'] = $_GET['density'];
	$form_params['temp_heater'] = $_GET['temp_heater'];
	$form_params['temp_solid'] = $_GET['temp_solid'];
	$form_params['heater_type'] = $_GET['heater_type'];
	$form_params['size_relation'] = $_GET['size_relation'];
	$form_params['placement'] = $_GET['placement'];
	
	$hcalc = new HeaterCalculator($form_params, "calc_heater");
	$result = array();
	$hcalc->calc();
	$res = $hcalc->getJSONResult();
	print $res;
?>