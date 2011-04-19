<?php

class GenericObjectCollection
{
	private $_tableName;
	private $_className;
	private $_itemsCount;
	private $_idArray;
	private $_objArray;
	private $_db;
	/**
	 * @var string ��� �������, ������� ���������� ��� ������ generic. ���� �� ������, �� ������������ $_className
	 */
	private $_classNameFunc;
	
	public function __construct($table_name, $class_name, $db) {
		$this->_tableName = $table_name;
		$this->_className = $class_name;
		$this->_db = $db;
	}
	
	public function addTuple($id) {
		if (! $this->id_array) {
			$this->_idArray = array();
		}
		array_push($this->_idArray, $id);
		$this->_itemsCount = count($this->_idArray);
	}
	
	public function getItemCount() {
		return $this->_itemsCount;
	}
	
	public function setClassNameFunc($func) {
		$this->_classNameFunc = $func;
	}
	
	public function populateObjectArray() {
		if ($this->_itemsCount > 0) {
			$query = "SELECT * FROM `".$this->_db->getDBName()."`.`$this->_tableName` WHERE id IN (".$this->_getCommaSeparatedIdList().")";
			try {
				$res = $this->_db->select($query);
				foreach ($res as $row) {
					$id = $row["id"];
					$index = $this->_getIndexForTuple($id);
					if ($index >= 0) {
						$robj = &$this->_objArray[$index];
						$class_name = $this->_className;
						if (isset($this->_classNameFunc))
							$class_name = call_user_func($this->_classNameFunc, $row);
						$s  = "\$robj = new $class_name($id, \$this->_db);";
						eval($s);
						$robj->forceLoaded();
						foreach ($row as $key => $value) {
							if (! is_numeric($key)) {
								$robj->__set($key, $value);
							}
						}
					}
				}
			}
			catch (Exception $e) {
				print $e->getMessage();
			}
		}
	}
	
	public function getPopulatedObjects() {
		$return_array = array();
		foreach ($this->_idArray as $id) {
			$index = $this->_getIndexForTuple($id);
			if ($index >= 0) {
				$return_array[$id] = $this->_objArray[$index];
			}			
		}
		return $return_array;
	}
	
	/*public function getObject($id) {
		$index = $this->_getIndexForTuple($id);
		if ($index < 0) {
			return null;
		}
		return $this->_objArray[$index];
	}*/
	
	private function _getCommaSeparatedIdList() {
		for ($i = 0; $i < count($this->_idArray); $i++) {
			if (is_numeric($this->_idArray[$i])) {
				$s .= $this->_idArray[$i].",";
			}
		}
		$s = substr($s, 0, strlen($s) - 1);
		return $s;
	}
	
	private function _getIndexForTuple($id) {
		$index = -1;
		$i = array_search($id, $this->_idArray);
		if ($i !== false && is_numeric($i)) {
			$index = $i;
		}
		return $index;
	}
}

?>