<?php
class Df_KazpostEms_Model_Request_Countries extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param Df_Directory_Model_Country $country
	 * @return int|null
	 */
	public function getCountryId(Df_Directory_Model_Country $country) {
		return df_array_first($this->getCountryData($country));
	}

	/**
	 * @param Df_Directory_Model_Country $country
	 * @return int|null
	 */
	public function getZone(Df_Directory_Model_Country $country) {
		return df_array_last($this->getCountryData($country));
	}

	/**
	 * @override
	 * @param string $responseAsText
	 * @return string
	 */
	public function preprocessJson($responseAsText) {
		return
			strtr(
				df_trim($responseAsText)
				,array(
					'value' => '"value"'
					,'text' => '"text"'
					,"'" => '"'
				)
			)
		;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array(
			'Accept' => 'application/json, text/javascript, */*'
			, 'Accept-Encoding'	=> 'gzip, deflate'
			, 'Accept-Language'	=> 'en-US,en;q=0.5'
			, 'Connection' => 'keep-alive'
			, 'Host' => 'emscal.kazpost.kz'
			, 'Referer' => 'http://emscal.kazpost.kz/'
			, 'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0'
			, 'X-Requested-With' => 'XMLHttpRequest'
		);
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return self::m(__CLASS__, 'getMapFromNameToData');}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return $this->getPropertiesToCache();}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'emscal.kazpost.kz';}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getQueryParams() {return array('kz' => 1);}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/inc/country.php';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needConvertResponseFrom1251ToUtf8() {return true;}

	/**
	 * @param Df_Directory_Model_Country $country
	 * @return int[]|null
	 */
	private function getCountryData(Df_Directory_Model_Country $country) {
		/** @var string $result */
		$result = df_a($this->getMapFromNameToData(), $this->normalizeName($country->getNameRussian()));
		if (!is_array($result)) {
			df_error(
				'Доставка службой EMS Kazpost %s невозможна.'
				, $country->getNameInFormDestination()
			);
		}
		return $result;
	}
	
	/** @return array(string => int[]) */
	private function getMapFromNameToData() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => int) $result  */
			$result = array();
			foreach ($this->response()->json() as $option) {
				/** @var array(string => string) $optionValue */
				$optionValue = df_a($option, 'value');
				df_assert_string_not_empty($optionValue);
				/** @var string[] $optionValueAsArray */
				$optionValueAsArray = explode('&', $optionValue);
				df_assert_eq(2, count($optionValueAsArray));
				/** @var string $countryName */
				$countryName = df_trim(df_a($option, 'text'));
				df_assert_string_not_empty($countryName);
				$countryName = $this->normalizeName($countryName);
				$result[$countryName] = rm_int($optionValueAsArray);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function normalizeName($name) {return mb_strtoupper(df_trim($name));}

	const _CLASS = __CLASS__;
	/** @return Df_KazpostEms_Model_Request_Countries */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}