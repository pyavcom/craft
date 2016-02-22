<?php
class Df_InTime_Model_Request_Locations_ForRate extends Df_InTime_Model_Request {
	/** @return array(string => int) */
	public function getLocations() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => int) $result */
			/** @var string $cacheKey */
			$cacheKey =
				$this->getCache()->makeKey(
					array($this, __FUNCTION__)
					, $this->getRegionExternalId()
				)
			;
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result = $this->parseLocations();
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(),array(
			'region_to' => $this->getRegionExternalId())
		);
	}

	/** @return int */
	private function getRegionExternalId() {
		/** @var int $result */
		$result =
			df_a(
				array(
					'ВИННИЦКАЯ ОБЛАСТЬ' => 22
					,'ВОЛЫНСКАЯ ОБЛАСТЬ' => 23
					,'ДНЕПРОПЕТРОВСКАЯ ОБЛАСТЬ' => 4
					,'ДОНЕЦКАЯ ОБЛАСТЬ' => 5
					,'ЖИТОМИРСКАЯ ОБЛАСТЬ' => 25
					,'ЗАКАРПАТСКАЯ ОБЛАСТЬ' => 7
					,'ЗАПОРОЖСКАЯ ОБЛАСТЬ' => 24
					,'ИВАНО-ФРАНКОВСКАЯ ОБЛАСТЬ' => 6
					,'КИЕВ' => 11
					,'КИЕВСКАЯ ОБЛАСТЬ' => 11
					,'КИРОВОГРАДСКАЯ ОБЛАСТЬ' => 12
					,'ЛУГАНСКАЯ ОБЛАСТЬ' => 14
					,'ЛЬВОВСКАЯ ОБЛАСТЬ' => 15
					,'НИКОЛАЕВСКАЯ ОБЛАСТЬ' => 16
					,'ОДЕССКАЯ ОБЛАСТЬ' => 17
					,'ПОЛТАВСКАЯ ОБЛАСТЬ' => 18
					,'СЕВАСТОПОЛЬ' => 13
					,'КРЫМ АВТОНОМНАЯ РЕСПУБЛИКА' => 13
					,'РОВЕНСКАЯ ОБЛАСТЬ' => 19
					,'СУМСКАЯ ОБЛАСТЬ' => 20
					,'ТЕРНОПОЛЬСКАЯ ОБЛАСТЬ' => 21
					,'ХАРЬКОВСКАЯ ОБЛАСТЬ' => 8
					,'ХЕРСОНСКАЯ ОБЛАСТЬ' => 9
					,'ХМЕЛЬНИЦКАЯ ОБЛАСТЬ' => 10
					,'ЧЕРКАССКАЯ ОБЛАСТЬ' => 1
					,'ЧЕРНИГОВСКАЯ ОБЛАСТЬ' => 2
					,'ЧЕРНОВИЦКАЯ ОБЛАСТЬ' => 3
				)
				,mb_strtoupper(
					$this->getRegionName()
				)
			)
		;
		df_result_integer($result);
		return $result;
	}

	/** @return string */
	private function getRegionName() {return $this->cfg(self::P__REGION_NAME);}

	/** @return array(string => int) */
	private function parseLocations() {
		/** @var array(string => int) $resultAsAssocArray */
		$resultAsAssocArray =
			array_merge(
				// array_flip нужно для корректного слияния массивов,
				// ибо иначе array_merge подумает, что у нас массив - неассоциативный
				// (целочисленные ключи)
				array_flip($this->response()->json('big'))
				,array_flip($this->response()->json('small'))
			)
		;
		return
			array_combine(
				rm_uppercase(array_keys($resultAsAssocArray))
				,rm_int(array_values($resultAsAssocArray))
			)
		;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__REGION_NAME, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__REGION_NAME = 'region_name';
	/**
	 * @param string $locationName
	 * @param string $regionName
	 * @return int
	 */
	public static function getLocationIdByNameAndRegion($locationName, $regionName) {
		df_param_string($locationName, 0);
		df_param_string($regionName, 1);
		/** @var Df_InTime_Model_Request_Locations_ForRate $api */
		$api = self::i(array(self::P__REGION_NAME => $regionName));
		/** @var int $result */
		$result = df_a($api->getLocations(), mb_strtoupper(df_trim($locationName)));
		df_result_integer($result);
		return $result;
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_InTime_Model_Request_Locations_ForRate
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}