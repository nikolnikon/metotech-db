<?php

require_once 'class.generic_object.php';

class Alloy extends GenericObject
{
	public function __construct($id, $db) {
		$this->initialize($id, 'alloys', $db);
	}
	
	public function __toString() {
		$str .= $this->name;
		$str .= ':';
		$str .= $this->grade;
		return $str;
	}
}

?>