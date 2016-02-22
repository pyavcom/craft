<?php
class Df_Excel_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Excel_Helper_Data */
	public function init() {
		Df_Excel_Helper_Lib::s();
		return $this;
	}

	/** @return Df_Excel_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}