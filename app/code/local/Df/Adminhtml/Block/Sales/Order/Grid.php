<?php
class Df_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {
	/**
	 * @param Varien_Data_Collection $collection
	 * @return void
	 */
	public function setCollection($collection) {
		/**
		 * Нам недостаточно события _load_before,
		 * потому что не все коллекции заказов используются для таблицы заказов,
		 * а в Magento 1.4 по коллекции невозможно понять,
		 * используется ли она для таблицы заказов или нет
		 * (в более поздних версиях Magento понять можно, потому что
		 * коллекция, используемая для таблицы заказов, принадлежит особому классу)
		 */
		Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareCollection::dispatch($collection);
		parent::setCollection($collection);
	}

	/**
	 * @override
	 * @return Df_Adminhtml_Block_Sales_Order_Grid
	 */
	protected function _prepareColumns() {
		parent::_prepareColumns();
		Mage::dispatchEvent(
			Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter::EVENT
			,array('grid' => $this)
		);
		// Учитывая, что обработчики вызванного выше события могли изменить столбцы,
		// столбцы надо упорядочить заново.
		$this->sortColumnsByOrder();
		return $this;
	}
}