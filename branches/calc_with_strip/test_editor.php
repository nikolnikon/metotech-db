<?php

	header('Content-Type: text/xml');
	$str = "<?xml version='1.0' encoding='utf-8'?>
	\n<response>
	
	\n</response>";
	file_put_contents("test_txt.txt", $str);
	
	$sx_1 = new SimpleXMLElement($str);
	$sx_1->addChild("success", "true");
	$sx_1->addChild("message", "GOOD BYE");
	$sx_1->addChild("data");
	$item = $sx_1->data->addChild("item");
	$item->addChild("id", 6);
	$item->addChild("alloy_name", "Никель");
	$item->addChild("grade", "НП2");
	$item->addChild("prod_name", "Проволока");
	$item->addChild("note");
	$item->addChild("diameter", "1.2");
	$item->addChild("length");
	$item->addChild("width");
	$item->addChild("thickness");
	$item->addChild("other_dim");
	$item->addChild("quantity", 3);
	$item->addChild("mass", "250");
	$item->addChild("price", "1300");
	//$item->addChild("order", "0");
	
	print $sx_1->asXML();
	file_put_contents("test_sx.xml", $sx_1->asXML());
	
	
	/*$sx = new SimpleXMLElement($string);
	foreach ($this->_priceItemsArray as $price_item) {
		$alloy = $this->_alloysArray[$price_item->alloy_id];
		$product = $this->_productArray[$price_item->product_id];
		$item = $sx->items->addChild('item');
		$item->addChild('alloy_name', $alloy->alloy_name);
		$item->addChild('grade', $alloy->grade);
		$item->addChild('prod_name', $product->prod_name);
		$item->addChild('note', $product->note);
		$item->addChild('diameter', $product->diameter);
		$item->addChild('length', $product->length);
		$item->addChild('width', $product->width);
		$item->addChild('thickness', $product->thickness);
		$item->addChild('other_dim', $product->other_dim);
		$item->addChild('quantity', $product->quantity);
		$item->addChild('mass', $price_item->mass);
		$item->addChild('price', $price_item->price);
		$item->addChild('order', 0);
	}
	file_put_contents('test.xml', $sx->asXML());
	print $sx->asXML();*/
?>