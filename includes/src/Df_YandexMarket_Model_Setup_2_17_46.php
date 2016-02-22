<?php
class Df_YandexMarket_Model_Setup_2_17_46 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		Df_Catalog_Model_Resource_Installer_Attribute::s()->updateAttribute(
			$entityTypeId = Mage_Catalog_Model_Product::ENTITY
			,$id = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			,$field = 'backend_model'
			,$value = Df_YandexMarket_Model_System_Config_Backend_Category::_CLASS
		);
		rm_eav_reset();
	}

	/** @return Df_YandexMarket_Model_Setup_2_17_46 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}