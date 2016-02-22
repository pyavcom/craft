<?php
class Df_Catalog_Model_Product_Option extends Mage_Catalog_Model_Product_Option {
	/** @return Df_Catalog_Model_Product_Option */
	public function deleteWithDependencies() {
		$this->getValueInstance()->deleteValue($this->getId());
		$this->deletePrices($this->getId());
		$this->deleteTitles($this->getId());
		$this->delete();
		return $this;
	}

	const _CLASS = __CLASS__;
	const P__TITLE = 'title';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Product_Option
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Catalog_Model_Product_Option
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}