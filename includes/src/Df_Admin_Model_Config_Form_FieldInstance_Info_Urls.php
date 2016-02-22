<?php
class Df_Admin_Model_Config_Form_FieldInstance_Info_Urls
	extends Df_Admin_Model_Config_Form_FieldInstance {
	/** @return array(string => string) */
	public function getUrls() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed) $result */
			$result = array();
			foreach (Mage::app()->getStores() as $store) {
				/** @var Mage_Core_Model_Store $store */
				$result[$store->getName()] = $this->getUrlForStore($store);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	private function getUrlForStore(Mage_Core_Model_Store $store) {
		return Mage::getUrl($this->getUrlPath($store), $this->getUrlParams($store));
	}

	/**
	 * @param Mage_Core_Model_Store $store
	 * @return array(string => string)
	 */
	private function getUrlParams(Mage_Core_Model_Store $store) {
		/** @var array(mixed => mixed) $result */
		$result = array(
			'_nosid' => true
			/**
			 * Указывание значения Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK
			 * вместо значения по умолчанию Mage_Core_Model_Store::URL_TYPE_LINK
			 * позволяет нам избежать включения в адрес кода магазина:
			 * @see Mage_Core_Model_Store::getBaseUrl()
			 */
			, '_type' => Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK
			/**
			 * Указание магазина обязательно
			 * для корректного исключения из адресов index.php при необходимости,
			 * потому что иначе система сочтёт магазин административным,
			 * а для административного магазина
			 * она никогда не исключает index.php из адресов:
			 * @see Mage_Core_Model_Store::_updatePathUseRewrites()
			 */
			, '_store' => $store
		);
		if (!Mage::app()->isSingleStoreMode() && $this->needPassParametersAsQuery()) {
			$result['_query'] = array('store-view' => $store->getCode());
		}
		return $result;
	}

	/**
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	private function getUrlPath(Mage_Core_Model_Store $store) {
		/** @var string $result */
		$result = $this->getUrlPathBase();
		if (!Mage::app()->isSingleStoreMode() && !$this->needPassParametersAsQuery()) {
			$result = df_concat_url($result, 'store-view', $store->getCode());
		}
		return $result;
	}

	/** @return string */
	private function getUrlPathBase() {return $this->getConfigParam('rm_url_path_base', true);}

	/** @return bool */
	private function needPassParametersAsQuery() {
		return $this->isConfigNodeExist('rm_url_pass_parameters_as_query');
	}

	const _CLASS = __CLASS__;
}