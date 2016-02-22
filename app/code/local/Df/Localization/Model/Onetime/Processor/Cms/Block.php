<?php
class Df_Localization_Model_Onetime_Processor_Cms_Block
	extends Df_Localization_Model_Onetime_Processor_Cms {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {return array('content', 'title');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, Df_Cms_Model_Block::_CLASS);
	}
}