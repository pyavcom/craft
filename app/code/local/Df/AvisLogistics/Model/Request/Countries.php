<?php
class Df_AvisLogistics_Model_Request_Countries extends Df_AvisLogistics_Model_Request {
	/**
	 * @param Df_Directory_Model_Country $country
	 * @return int|null
	 */
	public function getCountryId(Df_Directory_Model_Country $country) {
		/** @var string $result */
		$result = df_a($this->getMapFromNameToId(), $this->normalizeName($country->getNameRussian()));
		if (is_null($result)) {
			$this->getMethod()->throwExceptionInvalidCountryDestination();
		}
		return $result;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return self::m(__CLASS__, 'getMapFromNameToId');}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return $this->getPropertiesToCache();}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/rus/calculator/';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}
	
	/** @return array(string => int) */
	private function getMapFromNameToId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->response()->options('#country1 option');
		}
		return $this->{__METHOD__};
	}

	/** @return Df_AvisLogistics_Model_Method */
	protected function getMethod() {return $this->cfg(self::P__METHOD);}

	/**
	 * @param string $name
	 * @return string
	 */
	private function normalizeName($name) {return mb_strtoupper(df_trim($name));}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__METHOD, Df_AvisLogistics_Model_Method::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__METHOD = 'method';
	/**
	 * @static
	 * @param Df_AvisLogistics_Model_Method $method
	 * @return Df_Exline_Model_Request_Rate
	 */
	public static function i(Df_AvisLogistics_Model_Method $method) {
		return new self(array(self::P__METHOD => $method));
	}
	/** @return Df_AvisLogistics_Model_Request_Countries */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}