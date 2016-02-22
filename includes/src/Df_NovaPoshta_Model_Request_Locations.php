<?php
class Df_NovaPoshta_Model_Request_Locations extends Df_NovaPoshta_Model_Request {
	/** @return array(string => int) */
	public function getLocations() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => int) $result */
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(array($this, __FUNCTION__));
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result = $this->parseLocations();
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => int) */
	private function parseLocations() {
		/** @var array(string => int) $result */
		$result = array();
		/** @var string $matchedResult */
		$matchedResult = $this->response()->match("#var cities \= \[([^\]]+)\];#mui");
		/** @var string $matchedResultTrimmed */
		$matchedResultTrimmed = df_trim($matchedResult, "\r\n, ");
		df_assert_string($matchedResultTrimmed);
		/** @var string $locationsAsJson */
		$locationsAsJson =
			rm_sprintf(
				'[%s]'
				,strtr(
					$matchedResultTrimmed
					,array(
						'id' => '"id"'
						,'value' => '"value"'
					)
				)
			)
		;
		df_assert_string($locationsAsJson);
		/** @var array(array(string => string)) $locationsAsAssocArray */
		$locationsAsAssocArray =
			/**
			 * Zend_Json::decode использует json_decode при наличии расширения PHP JSON
			 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
			 * @see Zend_Json::decode
			 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
			 * Обратите внимание,
			 * что расширение PHP JSON не входит в системные требования Magento.
			 * @link http://www.magentocommerce.com/system-requirements
			 * Поэтому использование Zend_Json::decode выглядит более правильным, чем json_decode.
			 */
			Zend_Json::decode($locationsAsJson)
		;
		df_assert_array($locationsAsAssocArray);
		foreach ($locationsAsAssocArray as $locationData) {
			/** @var array(string => string) $locationData */
			df_assert_array($locationData);
			/** @var string $locationName */
			$locationName = df_a($locationData, 'value');
			df_assert_string($locationName);
			/** @var int $locationId */
			$locationId = rm_nat(df_a($locationData, 'id'));
			/** @var string $locationNameKey */
			$locationNameKey = mb_strtoupper($locationName);
			df_assert_string($locationNameKey);
			$result[$locationNameKey]= $locationId;
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_NovaPoshta_Model_Request_Locations */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}