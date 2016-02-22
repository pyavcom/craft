<?php
class Df_Cms_Model_Resource_Page extends Mage_Cms_Model_Mysql4_Page {
	/**
	 *  @override
	 *  @param Mage_Core_Model_Abstract $object
	 *  @return bool
	 */
	protected function isValidPageIdentifier(Mage_Core_Model_Abstract $object) {
		return
				1
			===
				preg_match(
					/**
					 * Добавляем поддержку кириллицы.
					 */
					'/^[a-zа-яА-ЯёЁ0-9][a-zа-яА-ЯёЁ0-9_\/-]+(\.[a-zа-яА-ЯёЁ0-9_-]+)?$/u'
					,$object->getData('identifier')
				)
		;
	}

	const _CLASS = __CLASS__;
	/**
	 * @see Df_Cms_Model_Page::_construct()
	 * @see Df_Cms_Model_Resource_Page_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Cms_Model_Resource_Page */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}