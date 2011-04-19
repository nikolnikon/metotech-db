<?php

class GenericObjectCollection
{
	private $_tableName;
	private $_className;
	private $_itemsCount;
	private $_idArray;
	private $_objArray;
	private $_db;
	
	public function __construct($table_name, $class_name) {
		$this->_tableName = $table_name;
		$this->_className = $class_name;
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
	
	public function populateObjectArray() {
		if ($this->_itemsCount > 0) {
			$query = "SELECT * FROM `".$this->_db->getDBName()."`.`$this->_tableName` WHERE id IN (".$this->_getCommaSeparatedIdList().")";
		}
		try {
			$res = $this->_db->select(query);
			foreach ($res as $row) {
				$id = $row["id"];
				$index = $this->_getIndexForTuple($id);
				if ($index >= 0) {
					$robj = &$this->_objArray[$index];
					$s  = "\$robj = new $this->_className($id, \$this->_db);";
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
	
	public function getPopulatedObjects() {
		
	}
	
	public function getObject($id) {
		
	}
	
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
		if ($i && is_numeric($i)) {
			$index = $i;
		}
		return $index;
	}
}

?>