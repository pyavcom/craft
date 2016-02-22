<?php
class Df_Core_Helper_Lib extends Df_Core_Helper_Lib_Abstract {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getScriptsToInclude() {
		/** @var string $base */
		$base = 'fp' . DS;
		return array(
			$base . 'compiled'
			, $base . 'array'
			, $base . 'validation'
			, $base . 'text'
			, $base . 'xml'
			, $base . 'serialize'
			, $base . 'date'
			, $base . 'other'
			, $base . 'licensor'
			, $base . 'domain'
			, $base . 'db'
			, $base . 'catalog'
			, $base . '1c'
		);
	}

	/** @return Df_Core_Helper_Lib */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}