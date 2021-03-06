<?php
/**
 * @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent()
 */
abstract class Df_Tweaks_Model_Handler_Remover extends Df_Core_Model_Handler {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract protected function hasDataToShow();

	/**
	 * @abstract
	 * @return bool
	 */
	abstract protected function getBlockName();

	/**
	 * @abstract
	 * @return Df_Tweaks_Model_Settings_Remove
	 */
	abstract protected function getSettings();

	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if ($this->needToRemove()) {
			df()->layout()->removeBlock($this->getBlockName());
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_CLASS;
	}

	/** @return string[] */
	private function getApplicableConfigValues() {
		return
			array(
				$this->getSettings()->removeFromAll()
				,$this->getConfigValueForCurrentPage()
			)
		;
	}

	/** @return string */
	private function getConfigValueForCurrentPage() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getSettings()->removeFromAll();
			if (rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_INDEX_INDEX)) {
				$result = $this->getSettings()->removeFromFrontpage();
			}
			else if (rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)) {
				$result = $this->getSettings()->removeFromCatalogProductList();
			}
			else if (rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)) {
				$result = $this->getSettings()->removeFromCatalogProductView();
			}
			else if (rm_handle_presents(Df_Core_Model_Layout_Handle::CUSTOMER_ACCOUNT)) {
				$result = $this->getSettings()->removeFromAccount();
			}
			else if (rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOGSEARCH_RESULT_INDEX)) {
				$result = $this->getSettings()->removeFromCatalogSearchResult();
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getInvisibleStates() {
		/** @var string[] $result */
		$result = array(Df_Admin_Model_Config_Source_RemoveIfEmpty::VALUE__REMOVE);
		if (!$this->hasDataToShow()) {
			$result[]= Df_Admin_Model_Config_Source_RemoveIfEmpty::VALUE__REMOVE_IF_EMPTY;
		}
		return $result;
	}

	/** @return bool */
	private function needToRemove() {
		return !!array_intersect($this->getApplicableConfigValues(), $this->getInvisibleStates());
	}

	const _CLASS = __CLASS__;
}