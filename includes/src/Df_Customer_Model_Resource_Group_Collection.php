<?php
class Df_Customer_Model_Resource_Group_Collection extends Mage_Customer_Model_Entity_Group_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Customer_Model_Group::mf(), Df_Customer_Model_Resource_Group::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Customer_Model_Resource_Group_Collection */
	public static function i() {return new self;}
}