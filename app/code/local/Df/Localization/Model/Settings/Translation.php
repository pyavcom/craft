<?php
class Df_Localization_Model_Settings_Translation extends Df_Core_Model_Settings {
	/** @return Df_Localization_Model_Settings_Area */
	public function admin() {return $this->getArea('admin');}
	/** @return Df_Localization_Model_Settings_Area */
	public function email() {return $this->getArea('email');}
	/** @return Df_Localization_Model_Settings_Area */
	public function frontend() {return $this->getArea(Df_Core_Const_Design_Area::FRONTEND);}
	/**
	 * @param string $name
	 * @return Df_Localization_Model_Settings_Area
	 */
	private function getArea($name) {
		df_param_string($name, 0);
		if (!isset($this->{__METHOD__}[$name])) {
			$this->{__METHOD__}[$name] = Df_Localization_Model_Settings_Area::i($name);
		}
		return $this->{__METHOD__}[$name];
	}
	/** @return Df_Localization_Model_Settings_Translation */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}