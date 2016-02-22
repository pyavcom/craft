<?php
class Df_Excel_Helper_Lib extends Df_Core_Helper_Lib_Abstract {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getScriptsToInclude() {
		return array('PHPExcel');
	}

	/** @return Df_Excel_Helper_Lib */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
