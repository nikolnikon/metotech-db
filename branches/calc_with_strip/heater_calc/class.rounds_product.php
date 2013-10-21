<?php

require_once 'class.product.php';

class RoundsProduct extends Product {
	
	public function getProdSize() {
		$size = "&Oslash; $this->diameter";
		return $size;
	}
}

?>