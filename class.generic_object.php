<?php

require_once 'class.mysql_dbase.php';

class GenericObject
{
	private $_id;
	private $_tableName;
	private $_dbFields = array();
	private $_loaded;
	private $_modifiedFields = array();
	private $_modified;
	private $_db;
	
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
			}
		}
		catch(Exception $e) {
			echo $e->getMessage();
			return;
		}
	}
	
	private function _load() {
		$this->reload();
		$this->_loaded = true;
	}
	
	public function forceLoaded() {
		$this->_loaded = true;
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
		$this->_dbFields[$field] = $value;
		$this->_modifiedFields[$field] = true;
		$this->_modified = true;			
	}
	
	public function remove() {
		
	}
	
	public function save() {
		
	}
}
?>