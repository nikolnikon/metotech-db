<?php

require_once 'class.product.php';

class SheetProduct extends Product {
	
	public function __toString() {
		/*$str .= $this->prod_name;
		$str .= ':';*/
		$str .= $this->thickness;
		$str .= 'x';
		$str .= $this->width;
		$str .= 'x';
		$str .= $this->length;
		return $str;
	} 
}

?>