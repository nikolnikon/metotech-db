<?php

require_once 'class.product.php';

class SheetProduct extends Product {
	
	public function getProdSize() {
		$size = $this->thickness."x".$this->width."x".$this->length;
		return $size;
	}
}

?>