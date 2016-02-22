<?php
class Df_Zf_Helper_Lib extends Df_Core_Helper_Lib_Abstract {
	/** @return array */
	protected function getScriptsToInclude() {
		return array('fp');
	}

	/** @return Df_Zf_Helper_Lib */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}