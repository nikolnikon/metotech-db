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
	public function getJSONResult()
	{
		$resp_array = $this->_result;
		if ($this->_success) {
			$resp_array['status'] = "success";
		}
		else {
			switch ($this->_errorCode) {
				case DBERROR:
					$resp_array['status'] = "db_error";
					$resp_array['error_header'] = "Ошибка БД";
					$resp_array['error_message'] = "Запрос к БД не может быть выполнен. Повторите попытку расчета позже";
					break;
				case DATAERROR:
					$resp_array['status'] = "data_error";
					$resp_array['error_header'] = "Ошибка данных";
					$resp_array['error_message'] = "Не удалось получить необходимые для вычислений данные. Повторите попытку расчета позже";
					break;
				case CALCERROR:
					$resp_array['status'] = "calc_error";
					$resp_array['error_header'] = "Ошибка при расчете";
					$resp_array['error_message'] = "Не удалось получить необходимые исходные данные. Повторите попытку расчета позже";
					break;
			}
		}
		$jsn = json_encode($resp_array);
		return $jsn;
	}
	
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
				$query = "SELECT `surface_power` FROM `metotech_metalls`.`heater_surface_power` WHERE `temp_solid` = ".mysql_real_escape_string($ts)." AND temp_heater = ".mysql_real_escape_string($th);
				$res = $db->select($query);
				if (! isset($res)) {
					;// неудачная попытка загрузки... в БД нет соответствующих данных
				}
				$this->_parameters['B_EF'] = $res[0]['surface_power'];
			}
			
			// определение поправочного коэффициента для расчета изменения электрического сопротивления в зависимости от температуры
			$id_mat = $form_params['material'];
			$query = "SELECT `correction_coef` FROM `metotech_metalls`.`var_resistent_coef` WHERE `alloy_id` = ".mysql_real_escape_string($id_mat)." AND temp = (SELECT MIN(`temp`) FROM `metotech_metalls`.`var_resistent_coef` WHERE `temp` >= ".mysql_real_escape_string($th).")";
			unset($res);
			$res = $db->select($query);
			if (! isset($res)) {
				;// неудачная попытка загрузки... в БД нет соответствующих данных
			}
			$this->_parameters['K'] = $res[0]['correction_coef'];
			
			//echo '<br><br> _parameters: '; print_r($this->_parameters); echo '<br><br>';
		} catch (Exception $e) {
			$this->_errorCode = DBERROR;
			return false;
			//print json_encode(array("status"=>"db_error", "error_header"=>"Ошибка БД", "error_message"=>$e->getMessage()));
		}
		return true;
	}
	
	/**
	 * Выполняет округление полученного диаметра до стандартного, округление длины и расчет массы проволоки (в том числе для трехфазного подключения) 
	 */
	protected function _handleCalc() {
		$d = $this->_result['D'];
		try {
			$db = MySqlDBase::instance();
			$d *= pow(10, 3);
			
			$query = "SELECT MIN(`standart_diameter`) FROM `metotech_metalls`.`standart_nom_diameters` WHERE `standart_diameter` > ".mysql_real_escape_string($d);
			$res = $db->select($query);
			if (isset($res[0]['MIN(`standart_diameter`)'])) {
				$d = $res[0]['MIN(`standart_diameter`)'];
			}
			
//			$this->_result['D_CALC'] = $d;
//			$this->_result['L_CALC'] = ceil($this->_result['L']);
//			$this->_result['M_CALC'] = $this->_result['L_CALC'] * $this->_parameters['DENS'] * pow(10, 3) * M_PI * pow($this->_result['D_CALC'], 2) * 0.25 * pow(10, -6);
//			$this->_result['M_CALC'] = round($this->_result['M_CALC'], 1);
			
			$this->_result['D'] = round($d, 1);
			$this->_result['L'] = ceil($this->_result['L']);
			$this->_result['M'] = $this->_result['L'] * $this->_parameters['DENS'] * pow(10, 3) * M_PI * pow($this->_result['D'], 2) * 0.25 * pow(10, -6);
			$this->_result['M'] = round($this->_result['M'], 3);
			// echo "result_array: "; print_r($this->_result); echo "\n";
		} catch(Exception $e) {
			$this->_errorCode = DBERROR;
			return false;
		}
		return true;
	}
}
?>