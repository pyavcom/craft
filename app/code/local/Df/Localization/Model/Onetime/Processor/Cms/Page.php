<?php
class Df_Localization_Model_Onetime_Processor_Cms_Page
	extends Df_Localization_Model_Onetime_Processor_Cms {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {
		return array('content', 'content_heading', 'layout_update_xml', 'title');
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, Df_Cms_Model_Page::_CLASS);
	}
}