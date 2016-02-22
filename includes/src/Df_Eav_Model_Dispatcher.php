<?php
class Df_Eav_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_collection_abstract_load_after(Varien_Event_Observer $observer) {
		/**
		 * Здесь нельзя вызывать df_enabled из-за рекурсии:
		 *
				df_enabled( )	..\Dispatcher.php:17
				Df_Licensor_Model_Feature->isEnabled( )	..\other.php:438
				Df_Licensor_Model_Feature->calculateEnabled( )	..\Feature.php:66
				Df_Licensor_Helper_Data->getStores( )	..\Feature.php:130
				Df_Licensor_Model_Collection_Store->loadAll( )	..\Data.php:91
				Varien_Data_Collection->getIterator( )	..\Collection.php:213
				Mage_Core_Model_Resource_Store_Collection->load( )	..\Collection.php:729
				Varien_Data_Collection_Db->load( )	..\Collection.php:174
				Mage_Core_Model_Resource_Db_Collection_Abstract->_afterLoad( )	..\Db.php:536
				Mage::dispatchEvent( )	..\Abstract.php:634
				Mage_Core_Model_App->dispatchEvent( )	..\Mage.php:416
				Mage_Core_Model_App->_callObserverMethod( )	..\App.php:1288
				Df_Eav_Model_Dispatcher->core_collection_abstract_load_after( )	..\App.php:1307
				df_enabled( )	..\Dispatcher.php:17
		 */
		try {
			/**
			 * Для ускорения работы системы проверяем класс коллекции прямо здесь,
			 * а не в обработчике события.
			 * Это позволяет нам не создавать обработчики событий для каждой коллекции.
			 */
			$collection = $observer->getData('collection');
			/** @var bool $needProcess */
			static $needProcess;
			if (!isset($needProcess)) {
				$needProcess = df_cfg()->localization()->translation()->admin()->isEnabled();
			}
			if (
					$needProcess
				&&
					df_h()->eav()->check()->entityAttributeCollection($collection)
			) {
				foreach ($collection as $attribute) {
					/** @var Mage_Eav_Model_Entity_Attribute $attribute */
					Df_Eav_Model_Translator::s()->translateAttribute($attribute);
				}
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function eav_entity_attribute_load_after(Varien_Event_Observer $observer) {
		try {
			/** @var bool $needProcess */
			static $needProcess;
			if (!isset($needProcess)) {
				$needProcess =
						df_cfg()->localization()->translation()->admin()->isEnabled()
					&&
						df_enabled(Df_Core_Feature::LOCALIZATION)
				;
			}
			if ($needProcess) {
				Df_Eav_Model_Translator::s()->translateAttribute($observer->getData('attribute'));
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}