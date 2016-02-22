<?php
class Df_PageCache_Model_Validator extends Df_Core_Model_Abstract {
	/**
	 * @param mixed $object
	 * @return Df_PageCache_Model_Validator
	 */
	public function checkDataChange($object) {
		if (array_intersect($this->_dataChangeDependency, $this->_getObjectClasses($object))) {
			$this->_invalidateCache();
		}
		return $this;
	}

	/**
	 * @param mixed $object
	 * @return Df_PageCache_Model_Validator
	 */
	public function checkDataDelete($object) {
		$classes = $this->_getObjectClasses($object);
		$intersect = array_intersect($this->_dataDeleteDependency, $classes);
		if (!empty($intersect)) {
			$this->_invalidateCache();
		}
		return $this;
	}

	/**
	 * Mark full page cache as invalidated
	 * @return void
	 */
	private function _invalidateCache() {
		Mage::app()->getCacheInstance()->invalidateType('full_page');
	}

	/**
	 * @param $object
	 * @return string[]
	 */
	private function _getObjectClasses($object) {
		/** @var string[] $result */
		$result = array();
		if (is_object($object)) {
			$result[]= get_class($object);
			$parent = $object;
			while (true) {
				/** @var string $parentClass */
				$parentClass = get_parent_class($parent);
				if (!$parentClass) {
					break;
				}
				$result[]= $parentClass;
				$parent = $parentClass;
			}
		}
		return $result;
	}

	/** @var string[] */
	private $_dataChangeDependency =
		array(
			'Mage_Catalog_Model_Product',
			'Mage_Catalog_Model_Category',
			'Mage_Catalog_Model_Resource_Eav_Attribute',
			'Mage_Tag_Model_Tag',
			'Mage_Review_Model_Review'
		)
	;
	/** @var string[] */
	private $_dataDeleteDependency =
		array(
			'Mage_Catalog_Model_Category',
			'Mage_Catalog_Model_Resource_Eav_Attribute',
			'Mage_Tag_Model_Tag',
			'Mage_Review_Model_Review'
		)
	;
	const _CLASS = __CLASS__;
	/** @return Df_PageCache_Model_Validator */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}