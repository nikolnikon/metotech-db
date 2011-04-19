<?php

require_once 'class.product.php';

class OtherProduct extends Product {
	
	public function __toString() {
		/*$str .= $this->prod_name;
		$str .= ':';*/
		$str .= $this->other_dim;
		return $str;
	} 
}

?>