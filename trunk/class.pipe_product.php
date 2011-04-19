<?php

require_once 'class.product.php';

class PipeProduct extends Product {
	
	public function __toString() {
		/*$str .= $this->prod_name;
		$str .= ':';*/
		$str .= '&Oslash; ';
		$str .= $this->diameter;
		$str .= 'x';
		$str .= $this->thickness;
		return $str;
	} 
}

?>