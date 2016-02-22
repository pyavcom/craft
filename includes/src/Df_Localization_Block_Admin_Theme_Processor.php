<?php
class Df_Localization_Block_Admin_Theme_Processor extends Df_Core_Block_Admin {
	/** @return string */
	public function getActionTitle() {return '';}

	/** @return string */
	public function getCssClass() {return 'rm-' . $this->getProcessor()->getType();}

	/** @return string */
	public function getLink() {return $this->getProcessor()->getLink();}

	/** @return string */
	public function getLinkTitle() {return '';}

	/** @return string */
	public function getTitle() {return df_escape($this->getProcessor()->getTitle());}

	/** @return string|null */
	protected function getDefaultTemplate() {return 'df/localization/theme/processor.phtml';}

	/** @return Df_Localization_Model_Onetime_Processor */
	protected function getProcessor() {return $this->cfg(self::P__PROCESSOR);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PROCESSOR, Df_Localization_Model_Onetime_Processor::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__PROCESSOR = 'processor';

	/**
	 * @param Df_Localization_Model_Onetime_Processor $processor
	 * @return string
	 */
	public static function getBlockClass(Df_Localization_Model_Onetime_Processor $processor) {
		return __CLASS__ . '_' . ucfirst($processor->getType());
	}
}