<?php
class Df_Localization_Model_Settings_Area extends Df_Core_Model_Settings_Group {
	/** @return string */
	public function allowInterference() {
		return $this->getValue('allow_interference');
	}
	/** @return boolean */
	public function isEnabled() {
		return $this->getYesNo('enabled', 'rm_translation');
	}
	/** @return boolean */
	public function needHideDecimals() {
		return $this->getYesNo('hide_decimals');
	}
	/** @return boolean */
	public function needSetAsPrimary() {
		return
				$this->isEnabled()
			&&
				$this->getYesNo('set_as_primary', array('rm_translation'))
		;
	}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->addData(array(self::P__SECTION => 'df_localization'));
	}
	/**
	 * @static
	 * @param string $group
	 * @return Df_Localization_Model_Settings_Area
	 */
	public static function i($group) {return new self(array(self::P__GROUP => $group));}
}