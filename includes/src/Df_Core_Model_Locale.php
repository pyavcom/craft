<?php
class Df_Core_Model_Locale extends Mage_Core_Model_Locale {
	/**
	 * Create Zend_Currency object for current locale
	 * @override
	 * @param  string $currency
	 * @return  Zend_Currency
	 */
	public function currency($currency) {
		try {
			$result = parent::currency($currency);
		}
		catch(Exception $e) {
			$result = $this->currencyDf($currency);
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getCurrency() {
		/** @var string $result */
		$result = Mage::getStoreConfig(self::XML_PATH_DEFAULT_CURRENCY);
		return $result ? $result : parent::getCurrency();
	}

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	public function getJsPriceFormat() {
		/** @var bool $needHideDecimals */
		static $needHideDecimals;
		if (!isset($needHideDecimals)) {
			$needHideDecimals =
					df_enabled(Df_Core_Feature::LOCALIZATION)
				&&
					(
						df_is_admin()
						? df_cfg()->localization()->translation()->admin()->needHideDecimals()
						: df_cfg()->localization()->translation()->frontend()->needHideDecimals()
					)
			;
		}
		/** @var array(string => string|int) $result */
		$result = parent::getJsPriceFormat();
		if ($needHideDecimals) {
			$result['requiredPrecision'] = 0;
		}
		return $result;
	}

	/**
	 * Retrieve timezone option list
	 * @override
	 * @return array
	 */
	public function getOptionTimezones()
	{
		$options= array();
		$zones  = $this->getTranslationList(
			/**
			 * BEGIN PATCH
			 *
			 * Вместо 'windowstotimezone'
			 */
			'citytotimezone'
			/**
			 * END PATCH
			 */
		);
		ksort($zones);
		foreach ($zones as $code=>$name) {
			$name = trim($name);
			$options[]= array(
			   'label' => empty($name) ? $code : $name . ' (' . $code . ')',   'value' => $code,);
		}
		return $this->_sortOptionArray($options);
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTimezone() {
		/** @var string $result */
		$result = Mage::getStoreConfig(self::XML_PATH_DEFAULT_TIMEZONE);
		return $result ? $result : parent::getTimezone();
	}

	/**
	 * @override
	 * @return array(array(string => string))
	 */
	public function getTranslatedOptionLocales() {
		/**
		 * Вместо того, чтобы вываливать перед администратором список из 200 языков,
		 * оставляем в этом списке только 3 разумных.
		 */
		return array(
			array('value' => 'ru_RU', 'label' => 'Русский')
			, array('value' => 'en_US', 'label' => 'English')
			, array('value' => 'uk_UA', 'label' => 'Українська')
		);
	}

	/**
	 * Create Zend_Currency object for current locale
	 *
	 * @param  string $currency
	 * @return  Zend_Currency
	 */
	private function currencyDf($currency)
	{
		Varien_Profiler::start('locale/currency');
		if (!isset(self::$_currencyCache[$this->getLocaleCode()][$currency])) {
			try {
				$currencyObject = new Zend_Currency(
					array('currency' => $currency)
					,$this->getLocale());
			} catch (Exception $e) {
				$currencyObject = new Zend_Currency(
					array('currency' => $this->getCurrency())
					,$this->getLocale()
				);
				$options =
					array(
						'name' => $currency
						,'currency' => $currency
						,'symbol' => $currency
					)
				;
				$currencyObject->setFormat($options);
			}
			self::$_currencyCache[$this->getLocaleCode()][$currency] = $currencyObject;
		}
		Varien_Profiler::stop('locale/currency');
		return self::$_currencyCache[$this->getLocaleCode()][$currency];
	}

	const XML_PATH_DEFAULT_CURRENCY = 'general/locale/currency';
}