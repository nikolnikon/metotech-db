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
		$this->_parameters['ALLOY'] = $form_params['material'];
		$this->_parameters['TEMP_HEATER'] = $form_params['temp_heater']; 
		
		
		$ts = $form_params['temp_solid']; // температура тела
		$th = $form_params['temp_heater']; // температура печи (нагревателя)
		$maxth = $form_params['max_temp']; // максимальная рабочая температура нагревателя
		//echo '<br><br> maxth: '.$maxth.'<br><br>';
		//echo '<br><br> th: '.$th.'<br><br>';
		//echo '<br><br> ts: '.$ts.'<br><br>';
		
		try {
			$db = MySqlDBase::instance();
			
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
			$d = $res[0]['MIN(`standart_diameter`)'];
			$t = $this->_parameters['TEMP_HEATER'];
			$this->_result['D_CALC'] = $d;
			$this->_result['L_CALC'] = ceil($this->_result['L']);
			
			echo "d: ".$d."; t: ".$t."\n";
			
			$query = "SELECT MAX(`diameter`), MAX(`max_temp`) FROM `metalls`.`max_heater_temp` WHERE `alloy_id`=".$this->_parameters['ALLOY']." AND `diameter` <= $d";
			echo "query_low: ".$query."\n";
			unset($res);
			$res = $db->select($query);
			if (! isset($res)) {
				;// неудачная попытка загрузки... в БД нет соответствующих данных
			}
			$t_low = $res[0]['MAX(`max_temp`)'];
			$d_low = $res[0]['MAX(`diameter`)'];
			echo "d_low: ".$d_low."; t_low: ".$t_low."\n";
			if ($t <= $t_low) {
				;// расчет верный
			}
			else {
				$query = "SELECT MIN(`diameter`), MIN(`max_temp`) FROM `metalls`.`max_heater_temp` WHERE `alloy_id`=".$this->_parameters['ALLOY']." AND `diameter` > $d";
				echo "query_high: ".$query."\n";
				unset($res);
				$res = $db->select($query);
				if (! isset($res)) {
					;// неудачная попытка загрузки... в БД нет соответствующих данных
				}
				$t_high = $res[0]['MIN(`max_temp`)'];
				$d_high = $res[0]['MIN(`diameter`)'];
				echo "d_high: ".$d_high."; t_high: ".$t_high."\n";
				if ($t <= ($t_low/2 + $t_high/2)) { // выбираем min стандартный диаметр больший $d_low
					$query = "SELECT MIN(`standart_diameter`) FROM `metalls`.`standart_nom_diameters` WHERE `standart_diameter` > ".mysql_real_escape_string($d_low);
					unset($res);
					$res = $db->select($query);
					if (! isset($res)) {
						;// неудачная попытка загрузки... в БД нет соответствующих данных
					}
					$d = $res[0]['MIN(`standart_diameter`)'];
					$this->_parameters['D'] = $d;
					$this->calc(false);
				}
				else { // выбираем $d_high
					$d = $d_high;
					$this->_parameters['D'] = $d * pow(10, -3);
					$this->calc(false);
				}
			}
			
			$this->_result['D'] = $d;
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