<?php
class Df_Localization_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Localization_Model_Settings_Translation */
	public function translation() {return Df_Localization_Model_Settings_Translation::s();}
	/** @return Df_Localization_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}