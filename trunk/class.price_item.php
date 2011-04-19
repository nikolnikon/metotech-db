<?php

require_once 'class.generic_object.php';

class PriceItem extends GenericObject
{
	public function __construct($id, $db) {
		$this->initialize($id, 'general_price', $db);
	}
	
	public function __toString() {
		$str .= $this->mass;
		$str .= ':';
		$str .= "от $this->price";
		return $str;
	}
}

?>