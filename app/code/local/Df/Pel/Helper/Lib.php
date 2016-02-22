<?php
class Df_Pel_Helper_Lib extends Df_Core_Helper_Lib_Abstract {
	/**
	 * Обратите внимание, что родительский класс Mage_Catalog_Helper_Product_Url
	 * не является потомком класса Varien_Object,
	 * поэтому у нашего класса нет метода _construct,
	 * и мы перекрываем именно конструктор
	 * @override
	 * @throws Exception
	 * @return Df_Pel_Helper_Lib
	 */
	public function __construct() {
		parent::__construct();
		if (0 !== intval(ini_get('mbstring.func_overload'))) {
			throw new Exception('Df_Pel: you must disable mbstring.func_overload!');
		}
		return $this;
	}

	/** @return int */
	protected function getIncompatibleErrorLevels() {
		if (!defined('E_DEPRECATED')) {
			define('E_DEPRECATED', 8192);
		}
		return E_STRICT | E_NOTICE | E_WARNING | E_DEPRECATED;
	}

	/** @return Df_Pel_Helper_Lib */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}