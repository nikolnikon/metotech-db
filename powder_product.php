<?php

require_once 'product.php';

class PowderProduct extends Product {
	
	public function __toString() {
		$str .= $this->prod_name;
		$str .= ':';
		$str .= $this->other_dim;
		return $str;
	} 
}

?>