<?php

require_once 'class.product.php';

class OtherProduct extends Product {
	
	public function getProdSize() {
		$size = $this->other_dim;
		return $size;
	} 
}

?>