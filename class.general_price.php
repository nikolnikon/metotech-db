<?php

require_once 'class.generic_object.php';

class GeneralPrice extends GenericObject
{
	public function __construct($id, $db) {
		$this->initialize($id, 'general_price', $db);
	}
}

?>