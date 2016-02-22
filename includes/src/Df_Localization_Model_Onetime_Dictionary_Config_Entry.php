<?php
class Df_Localization_Model_Onetime_Dictionary_Config_Entry
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string */
	public function getPath() {return $this->getEntityParam('path');}
	/** @return string */
	public function getValue() {return $this->getEntityParam('value');}
	/** @return string|null */
	public function getValueOriginal() {return $this->getEntityParam('original_value');}
	/** @return string */
	public function isLowLevel() {return $this->isChildExist('low_level');}
	/** @return string */
	public function needSetAsDefault() {return $this->isChildExist('set_as_default');}

	/** Используется из @see Df_Localization_Model_Onetime_Dictionary_Config_Entries::getItemClass() */
	const _CLASS = __CLASS__;
}


 