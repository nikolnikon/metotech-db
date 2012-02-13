<?php
require_once 'class.generic_object.php';

/**
 * GenericObject, который соответствует таблице БД `metalls`.`rad_eff_coef`
 * @author nikonov
 *
 */
class RadEffCoef extends GenericObject
{
	public function __construct($id, $db) {
		$this->initialize($id, 'rad_eff_coef', $db);
	}
	
	public function __toString() {
		return $this->heater_placement;
	}
}
?>
