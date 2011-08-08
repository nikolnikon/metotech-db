<?php

require_once 'class.product.php';

class PipeProduct extends Product {
	
	public function getProdSize() {
		$size = "&Oslash; $this->diameter"."x".$this->thikness;
		if (! empty($this->length)) {
			$size .= "x$this->length";
		}
		return $size;
	} 
}

?>