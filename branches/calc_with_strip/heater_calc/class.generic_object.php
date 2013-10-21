<?php

require_once 'class.mysql_dbase.php';

class GenericObject
{
	/**
	 * id соответствующей записи
	 * @var int
	 */
	private $_id;
	/**
	 * Имя таблицы БД
	 * @var string
	 */
	private $_tableName;
	/**
	 * Содержит пары key->имя поля, value->значение поля
	 * @var array
	 */
	private $_dbFields = array();
	/**
	 * Показывает, загружены ли данные из БД в объект
	 * @var bool
	 */
	private $_loaded;
	/**
	 * Показывает, модифицировалось ли поле. key -> имя поля, value -> модиф./не модиф.
	 * @var array
	 */
	private $_modifiedFields = array();
	/**
	 * Истино, если с момента последней загрузки из БД что-либо было изменено.
	 * @var bool
	 */
	private $_modified;
	/**
	 * БД
	 * @var MySQLDBase
	 */
	private $_db;
	
	public function print_state() {
		echo '_modifiedFields: '; print_r($this->_modifiedFields); echo '<br>';
		echo '_modified: '; echo $this->_modified; echo '<br>';
	}
	
	public function initialize($id, $table_name, $db) {
		$this->_id = $id;
		$this->_tableName = $table_name;
		$this->_db = $db;
	}
	
	public function reload() {
		$query = "SELECT * FROM `".$this->_db->getDBName()."`.`$this->_tableName` WHERE `id` = '$this->_id'";
		try {
			$res = $this->_db->select($query);
			if (count($res) > 0) {
				$this->_dbFields = $res[0];
				$this->_loaded = true;
				if (sizeof($this->_modifiedFields) > 0) {
					foreach($this->_modifiedFields as $key => $value) {
						$this->_modifiedFields[$key] = false;
					}
				}
				$this->_modified = false;
			}
		}
		catch(Exception $e) {
			echo $e->getMessage();
			return;
		}
	}
	
	/**
	 * Возвращает имена полей БД, которые содержатся в объекте
	 * @return array массив имен полей БД
	 */
	public function getFields() {
		return array_keys($this->_dbFields);
	}
	
	private function _load() {
		$this->reload();
		$this->_loaded = true;
	}
	
	public function forceLoaded() {
		$this->_loaded = true;
	}
	
	/**
	 * Устанавливает объект в сохраненное состояние, не сохраняя изменения в БД. 
	 */
	public function forceSaved() {
		foreach ($this->_modifiedFields as $field => $value) {
			$this->_modifiedFields[$field] = false;
		}
		$this->_modified = false;
	}
	
	public function __get($field) {
		if (! $this->_loaded) {
			$this->_load();
		}
		
		if (array_key_exists($field, $this->_dbFields)) {
            return $this->_dbFields[$field];
        }
	}
	
	public function __set($field, $value) {
		if (! $this->_loaded) {
			if ($this->_id) {
				$this->_load();
			}
		}
		$this->_dbFields[$field] = $value;
		$this->_modifiedFields[$field] = true;
		$this->_modified = true;			
	}
	
	/**
	 * Сохраняет изменения в базу данных. Если изменен объект, который имеет id, то выполняется UPDATE соответствующей записи, если объект имеет "некорректный", то INSERT. 
	 * @return bool 
	 */
	public function save() {
		if (! $this->_id) {
			$this->_loaded = false;
		}
		if (! $this->_loaded) {
			// создаем новую запись в БД
			foreach ($this->_dbFields as $field => $value) {
				if ($value != "") {
					$ar[$field] = $value;
				}
			}
			try {
				$this->_id = $this->_db->insert($this->_tableName, $ar);
				$this->_dbFields['id'] = $this->_id;
			}
			catch (Exception $e) {
				echo $e->getMessage();
				return false;
			}
		}
		else {
			// обновляем существующую запись в БД
			$arConds['id'] = $this->_id; // плохо, что название поля с идентификатором жестко забито
			foreach ($this->_dbFields as $field => $value) {
				if ($this->_modifiedFields[$field] == true) {
					if ($value == "") {
						$ar[$field] = 'NULL';
					}
					else {
						$ar[$field] = $value;
					}
				}
			}
			try {
				$this->_db->update($this->_tableName, $ar, $arConds);
			}
			catch (Exception $e) {
				echo $e->getMessage();
				return false;
			}
		}
		foreach($this->_modifiedFields as $key => $value) {
			$this->_modifiedFields[$key] = false;
		}
		$this->_modified = false;
		$this->forceLoaded();
		return true;
	}
	
	public function remove() {
		if ($this->_id) {
			$arConds['id'] = $this->_id;
			try {
				$this->_db->delete($this->_tableName, $arConds);	
			}
		catch (Exception $e) {
				echo $e->getMessage();
				return false;
			}
		}
		return true;
	}
}
?>