<?php
class Df_Page_Model_Html_Head_JQuery_Google extends Df_Page_Model_Html_Head_JQuery_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getConfigSuffix() {return 'cdn';}

	/**
	 * @override
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	protected function processInternal($format, array &$staticItems) {
		return implode("\r\n", array(
			Df_Core_Model_Format_Html_Tag::scriptExternal($this->getPath())
			,Df_Core_Model_Format_Html_Tag::scriptLocal('jQuery.noConflict(); jQuery.migrateMute = true;')
			,Df_Core_Model_Format_Html_Tag::scriptExternal($this->getPathMigrate())
			,''
		));
	}

	/** @return Df_Page_Model_Html_Head_JQuery_Google */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}