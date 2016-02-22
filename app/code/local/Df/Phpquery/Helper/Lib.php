<?php
class Df_Phpquery_Helper_Lib extends Df_Core_Helper_Lib_Abstract {
	/**
	 * @override
	 * @return int
	 */
	protected function getIncompatibleErrorLevels() {
		return E_NOTICE;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getScriptsToInclude() {
		return array('phpQuery');
	}

	/** @return Df_Phpquery_Helper_Lib */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}