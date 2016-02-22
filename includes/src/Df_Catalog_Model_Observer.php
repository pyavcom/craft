<?php
class Df_Catalog_Model_Observer extends Mage_Catalog_Model_Observer {
	/**
	 * Переписал метод ядра @see Mage_Catalog_Model_Observer::addCatalogToTopmenuItems()
	 * ради ускорения его работы.
	 * Функциональность осталась неизменной.
	 * @override
	 * @param Varien_Event_Observer $observer
	 */
	public function addCatalogToTopmenuItems(Varien_Event_Observer $observer) {
		/**
		 * Перекрытый родительский метод работает так:
		 * parent::addCatalogToTopmenuItems($observer);
		 */
		Df_Catalog_Model_Processor_Menu::i($observer->getData('menu'))->process();
	}
}