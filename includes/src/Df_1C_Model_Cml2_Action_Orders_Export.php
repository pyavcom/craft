<?php
/**
 * Экспорт заказов из магазина в 1С:Управление торговлей
 */
class Df_1C_Model_Cml2_Action_Orders_Export extends Df_1C_Model_Cml2_Action {
	/**
	 * Для тестирования
	 * @override
	 * @return Zend_Date
	 */
	protected function getLastProcessedTime() {
		/** @var Zend_Date $result */
		$result = parent::getLastProcessedTime();
		/**
		 * для некоторых сценариев тестирования
		 */
		if (true && df_is_it_my_local_pc()) {
			$result = Zend_Date::now();
			/**
			 * Zend_Date::sub() возвращает число в виде строки для Magento CE 1.4.0.1
			 * и объект класса Zend_Date для более современных версий Magento
			 */
			$result->sub(7, Zend_Date::DAY);
		}
		return $result;
	}

	/**
	 * @overrode
	 * @return bool
	 */
	protected function needUpdateLastProcessedTime() {
		return true;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		rm_response_content_type($this->getResponse(), 'application/xml; charset=utf-8');
		if (df_is_it_my_local_pc()) {
			rm_file_put_contents(
				df_concat_path(Mage::getBaseDir('var'), 'log', 'site-from-my.xml')
				,$this->getDocument()->getXml()
			);
		}
		$this->getResponse()
			->setBody(
				true // false — для некоторых сценариев тестирования
				? $this->getDocument()->getXml()
				: $this->getDocumentFake()->getXml()
			)
		;
	}

	/** @return Df_1C_Model_Cml2_SimpleXml_Generator_Document_Orders */
	private function getDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_SimpleXml_Generator_Document_Orders::_i2($this->getOrders())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_SimpleXml_Generator_Document */
	private function getDocumentFake() {
		return Df_1C_Model_Cml2_SimpleXml_Generator_Document::i();
	}

	/** @return Df_Sales_Model_Resource_Order_Collection */
	private function getOrders() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sales_Model_Resource_Order_Collection $result */
			$result = Df_Sales_Model_Order::c();
			/** @var Zend_Db_Adapter_Abstract $adapter */
			$adapter = $result->getSelect()->getAdapter();
			// Отбраковываем из коллекции заказы других магазинов
			$result
				->addFieldToFilter(
					$adapter->quoteIdentifier(Df_Sales_Model_Order::P__STORE_ID)
					,rm_state()->getStoreProcessed()->getId()
				)
			;
			/**
			 * Магазин должен передавать в 1С: Управление торговлей 2 вида заказов:
			 * 1) Заказы, которые были созданы в магазине ПОСЛЕ последнего сеанса передачи данных
			 * 2) Заказы, которые были изменены в магазине ПОСЛЕ последнего сеанса передачи данных
			 * Как я понимаю, оба ограничения можно наложить единым фильтром:
			 * по времени изменения заказа.
			 */
			$result
				->addFieldToFilter(
					$adapter->quoteIdentifier(Df_Sales_Model_Order::P__UPDATED_AT)
					,array(
						Df_Varien_Const::FROM => $this->getLastProcessedTime()
						,Df_Varien_Const::DATETIME => true
					)
				)
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Orders_Export
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}