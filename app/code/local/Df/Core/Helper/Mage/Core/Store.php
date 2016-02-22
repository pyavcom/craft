<?php
class Df_Core_Helper_Mage_Core_Store extends Mage_Core_Helper_Abstract {
	/**
	 * Обратите внимание, что метод возвращает:
	 *
	 * 		объект класса Mage_Core_Model_Resource_Store_Collection
	 * 		для Magento версии меньше 1.6
	 *
	 * 		объект класса Mage_Core_Model_Mysql4_Store_Collection
	 * 		для Magento версии не меньше 1.6
	 * @return Mage_Core_Model_Resource_Store_Collection|Mage_Core_Model_Mysql4_Store_Collection
	 */
	public function collection() {
		/** @var Mage_Core_Model_Resource_Store_Collection|Mage_Core_Model_Mysql4_Store_Collection $result */
		$result = Mage::getResourceModel(Df_Core_Const::STORE_COLLECTION_CLASS_MF);
		$this->assertCollectionClass($result);
		$result->setLoadDefault(true);
		$this->assertCollectionClass($result);
		return $result;
	}

	/**
	 * @var Varien_Data_Collection_Db $storeCollection
	 * @return void
	 */
	public function assertCollectionClass(Varien_Data_Collection_Db $storeCollection) {
		df_assert(
			@class_exists('Mage_Core_Model_Resource_Store_Collection')
			? ($storeCollection instanceof Mage_Core_Model_Resource_Store_Collection)
			: ($storeCollection instanceof Mage_Core_Model_Mysql4_Store_Collection)
		);
	}

	const _CLASS = __CLASS__;
	/** @return Df_Core_Helper_Mage_Core_Store */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}