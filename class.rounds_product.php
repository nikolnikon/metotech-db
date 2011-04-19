<?php

require_once 'product.php';

class RoundsProduct extends Product {
	
	public function __toString() {
		$str .= $this->prod_name;
		$str .= ':';
		$str .= '&Oslash; ';
		$str .= $this->diameter;
		return $str;
	} 
}

?>