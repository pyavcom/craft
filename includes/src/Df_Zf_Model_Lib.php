<?php
/**
 * Удалять этот класс нельзя,
 * потому что метод @see Df_Zf_Model_Lib::init()
 * вызывается из config.xml
 */
class Df_Zf_Model_Lib extends Df_Core_Model_Lib_Abstract {
	/** @return void */
	public function init() {Df_Zf_Helper_Lib::s();}
	/** @return Df_Core_Model_Lib */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}