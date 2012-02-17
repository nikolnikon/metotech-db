<?php
require_once ('class.abstract_calculator.php');
require_once ('class.mysql_dbase.php');

/**
 * ����������� ������������
 * @author nikolnikon
 * @version 1.0
 * @created 07-���-2012 16:28:21
 */
class HeaterCalculator extends AbstractCalculator
{
	protected function _loadParameters($form_params)
	{
		$this->_parameters['U'] = $form_params['voltage'];
		$this->_parameters['GRD'] = $form_params['pgrid'];
		if ($form_params['pgrid'] == 1) {
			$this->_parameters['P'] = $form_params['power'];
		}
		elseif ($form_params['pgrid'] == 3) {
			$this->_parameters['P'] = $form_params['power'] / 3;
		}
		$this->_parameters['RO_20'] = $form_params['resistivity'];
		$this->_parameters['A'] = $form_params['placement'];
		
		$ts = $form_params['temp_heating']; // температура тела
		$th = $form_params['max_temp']; // максимальная рабочая температура нагревателя
		echo '<br><br> th: '.$th.'<br><br>';
		
		try {
			$db = MySqlDBase::instance();
			
			$query = "SELECT MIN(`temp_heater`) FROM `metalls`.`heater_surface_power` WHERE `temp_heater` > ".mysql_real_escape_string($ts)." AND temp_heater <= ".mysql_real_escape_string($th);
			$res = $db->select($query);
			echo '<br><br> res: '; print_r($res); echo '<br><br>';
			if (! isset($res)) {
				;// неудачная попытка загрузки... в БД нет соответствующих данных
			}
			$rth = $res[0]['MIN(`temp_heater`)']; // температура нагревателя, которая принимается при расчетах
			
			$query = "SELECT `surface_power` FROM `metalls`.`heater_surface_power` WHERE `temp_solid` = ".mysql_real_escape_string($ts)." AND temp_heater = ".mysql_real_escape_string($rth);
			unset($res);
			$res = $db->select($query);
			if (! isset($res)) {
				;// неудачная попытка загрузки... в БД нет соответствующих данных
			}
			$this->_parameters['B_EF'] = $res[0]['surface_power'];
			
			$id_mat = $form_params['material'];
			$query = "SELECT `correction_coef` FROM `metalls`.`var_resistent_coef` WHERE `alloy_id` = ".mysql_real_escape_string($id_mat)." AND temp = (SELECT MIN(`temp`) FROM `metalls`.`var_resistent_coef` WHERE `temp` >= ".mysql_real_escape_string($rth).")";
			unset($res);
			$res = $db->select($query);
			if (! isset($res)) {
				;// неудачная попытка загрузки... в БД нет соответствующих данных
			}
			$this->_parameters['K'] = $res[0]['correction_coef'];
			
			echo '<br><br> _parameters: '; print_r($this->_parameters); echo '<br><br>';
		} catch (Exception $e) {
			echo '<br><br>'.$e->getMessage().'<br>';
		}
		// return true;
	}
	
	/**
	 * Выполняет расчет массы проволоки (в том числе общей массы для трехфазного подключения) 
	 */
	protected function _handleCalc() {
		
	}
}
?>