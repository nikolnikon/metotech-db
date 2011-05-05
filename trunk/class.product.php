<?php

require_once 'class.generic_object.php';

abstract class Product extends GenericObject
{
	public function __construct($id, $db) {
		$this->initialize($id, 'production', $db);
	}
	
	public function getProdName() {
		$name = $this->prod_name;
		if (! empty($this->prod_note)) {
			$name .= " ($this->prod_note)";
			echo '<br><br>NOT EMPTY<br><br>';
		}
		return $name;
	}
	
	abstract public function getProdSize();
}

?>