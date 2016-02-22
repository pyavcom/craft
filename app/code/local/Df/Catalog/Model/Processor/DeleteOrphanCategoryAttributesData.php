<?php
/**
 * Если база данных находится в некорректном состоянии,
 * то при денормализации таблиц товарных разделов может произойти сбой:
 * «Undefined offset (index) in Mage/Catalog/Model/Resource/Category/Flat.php»:
 * @see Df_Catalog_Model_Resource_Category_Flat::_getAttributeValues()
 * Данный класс чинит базу данных.
 */
class Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData {
	/** @return void */
	public function process() {
		if (!$this->isProcessed()) {
			$this->processInternal();
			$this->setProcessed();
		}
	}

	/** @return int[] */
	private function getAttributeIds() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_conn()->fetchCol(
					rm_conn()->select()->from(rm_table('eav/attribute'), 'attribute_id')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getTablesToProcess() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array(rm_table('catalog/eav_attribute'));
			foreach (Df_Catalog_Model_Resource_Category_Flat::getAttributeTypes() as $type) {
				/** @var string $type */
				$result[]= $this->resource()->getTableByType($type);
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isProcessed() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getStoreConfigFlag(self::$_CONFIG_PATH);
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function processInternal() {
		foreach ($this->getTablesToProcess() as $table) {
			/** @var string $table */
			$this->processTable($table);
		}
	}

	/**
	 * @param string $table
	 * @return void
	 */
	private function processTable($table) {
		rm_conn()->delete($table, array('attribute_id NOT IN (?)' => $this->getAttributeIds()));
	}

	/** @return Df_Catalog_Model_Resource_Category_Flat */
	private function resource() {return Df_Catalog_Model_Resource_Category_Flat::s();}

	/** @return void */
	private function setProcessed() {
		Mage::getConfig()->saveConfig(self::$_CONFIG_PATH, 1);
		Mage::getConfig()->reinit();
	}

	/** @var string */
	private static $_CONFIG_PATH = 'df/catalog/orhran_category_attributes_data_has_been_deleted';

	/** @return Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}