<?php
require_once ('class.abstract_calculator.php');
require_once ('class.mysql_dbase.php');

/**
 * Класс, реализующий калькулятор для нагревателей
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
		$this->_parameters['DENS'] = $form_params['density'];
		
		$ts = $form_params['temp_solid']; // температура тела
		$th = $form_params['temp_heater']; // температура печи (нагревателя)
		$maxth = $form_params['max_temp']; // максимальная рабочая температура нагревателя
		//echo '<br><br> maxth: '.$maxth.'<br><br>';
		//echo '<br><br> th: '.$th.'<br><br>';
		//echo '<br><br> ts: '.$ts.'<br><br>';
		
		try {
			$db = MySqlDBase::instance();
			
			/*$query = "SELECT MIN(`temp_heater`) FROM `metalls`.`heater_surface_power` WHERE `temp_heater` > ".mysql_real_escape_string($ts)." AND temp_heater <= ".mysql_real_escape_string($th);
			$res = $db->select($query);
			echo '<br><br> res: '; print_r($res); echo '<br><br>';
			if (! isset($res)) {
				;// неудачная попытка загрузки... в БД нет соответствующих данных
			}
			$rth = $res[0]['MIN(`temp_heater`)']; // температура нагревателя, которая принимается при расчетах*/
			
			// определение удельной поверхностной мощности
			if ($th <= 300) { // если печь низкотемпературная, то удельная поверхностная мощность составляет 4-6 Вт/см^2. Выбираем среднее значение
				$this->_parameters['B_EF'] = 5;
			}
			else {
				$query = "SELECT `surface_power` FROM `metalls`.`heater_surface_power` WHERE `temp_solid` = ".mysql_real_escape_string($ts)." AND temp_heater = ".mysql_real_escape_string($th);
				$res = $db->select($query);
				if (! isset($res)) {
					;// неудачная попытка загрузки... в БД нет соответствующих данных
				}
				$this->_parameters['B_EF'] = $res[0]['surface_power'];
			}
			
			// определение поправочного коэффициента для расчета изменения электрического сопротивления в зависимости от температуры
			$id_mat = $form_params['material'];
			$query = "SELECT `correction_coef` FROM `metalls`.`var_resistent_coef` WHERE `alloy_id` = ".mysql_real_escape_string($id_mat)." AND temp = (SELECT MIN(`temp`) FROM `metalls`.`var_resistent_coef` WHERE `temp` >= ".mysql_real_escape_string($th).")";
			unset($res);
			$res = $db->select($query);
			if (! isset($res)) {
				;// неудачная попытка загрузки... в БД нет соответствующих данных
			}
			$this->_parameters['K'] = $res[0]['correction_coef'];
			
			//echo '<br><br> _parameters: '; print_r($this->_parameters); echo '<br><br>';
		} catch (Exception $e) {
			echo '<br><br>'.$e->getMessage().'<br>';
		}
		// return true;
	}
	
	/**
	 * Выполняет округление полученного диаметра до стандартного, округление длины и расчет массы проволоки (в том числе для трехфазного подключения) 
	 */
	protected function _handleCalc() {
		$d = $this->_result['D'];
		
		try {
			$db = MySqlDBase::instance();
			$d *= pow(10, 3);
			$query = "SELECT MIN(`standart_diameter`) FROM `metalls`.`standart_nom_diameters` WHERE `standart_diameter` > ".mysql_real_escape_string($d);
			$res = $db->select($query);
			if (! isset($res)) {
				;// неудачная попытка загрузки... в БД нет соответствующих данных
			}
			$this->_result['D'] = $res[0]['MIN(`standart_diameter`)'];
			$this->_result['L'] = ceil($this->_result['L']);
			$this->_result['M'] = $this->_result['L'] * $this->_parameters['DENS'] * pow(10, 3) * M_PI * pow($this->_result['D'], 2) * 0.25 * pow(10, -6);
			$this->_result['M'] = round($this->_result['M'], 1);
			//print_r($this->_result);
		} catch(Exception $e) {
			// обработка исключений от БД
		}
	}
}
?>