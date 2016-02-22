<?php
class Df_Page_Block_Template_Links extends Mage_Page_Block_Template_Links {
	/**
	 * @param string $blockType
	 * @return Df_Page_Block_Template_Links
	 */
	public function removeLinkByBlockType($blockType) {
		/** @var array $keysToUnset */
		$keysToUnset = array();
		foreach ($this->getLinks() as $key => $link) {
			/** @var Varien_Object $link */
			if ($link instanceof Mage_Core_Block_Abstract) {
				/** @var Mage_Core_Block_Abstract $link */
				if ($blockType === $link->getData("type")) {
 					$keysToUnset[]= $key;
				}
			}
		}
		foreach ($keysToUnset as $keyToUnset) {
			unset($this->_links[$keyToUnset]);
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfg()->speed()->blockCaching()->pageTemplateLinks()
		) {
			/**
			 * Ключ кэша не устанавливаем, потому что это делает родительский класс
			 * @see Mage_Page_Block_Template_Links::getCacheKeyInfo
			 */
			$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
		}
	}
}