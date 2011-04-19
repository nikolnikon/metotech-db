<?php

require_once 'class.generic_object.php';

class Product extends GenericObject
{
	public function __construct($id, $db) {
		$this->initialize($id, 'production', $db);
	}
}

?>