<?php

require_once 'class.product.php';

class StripProduct extends Product {
	
	public function getProdSize() {
		$size = $this->thickness.'x'.$this->width;
		return $size;
	}
}

?>