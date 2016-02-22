<?php
class Df_InTime_Model_Request_Locations_ForDeliveryTime extends Df_InTime_Model_Request {
	/** @return string */
	public function getLocationId() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			// Сначала ищем точное совпадение
			$result = df_a($this->getLocations(), $this->getLocationNameNormalized());
			if (is_null($result)) {
				// Теперь ищем вхождения в начале строки.
				// Например: ВИННИЦА ЗАМОСТЯНСКАЯ (ДО 30 КГ ЗА 1 МЕСТО)
				foreach ($this->getLocations() as $name => $id) {
					/** @var string $name */
					/** @var string $id */
					if (rm_starts_with($name, $this->getLocationNameNormalized())) {
						$result = $id;
						break;
					}
				}
				if (is_null($result)) {
					if (0 < count($this->getLocations())) {
						$result = df_array_first($this->getLocations());
					}
				}
			}
			if (is_null($result)) {
				df_error(
					'Служба In-Тайм не доставляет грузы в населённый пункт «%s»'
					,$this->getLocationName()
				);
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Accept' => 'application/json, text/javascript, */*; q=0.01'
			,'Referer' => 'http://www.intime.ua/shipment/'
		));
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getQueryParams() {
		return array_merge(parent::getQueryParams(), array(
			'term' => $this->getLocationName())
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/modules/shipment/cityes.php';}

	/** @return string */
	private function getLocationName() {return $this->cfg(self::P__LOCATION_NAME);}

	/** @return string */
	private function getLocationNameNormalized() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_strtoupper(df_trim($this->getLocationName()));
		}
		return $this->{__METHOD__};
	}

	/**
	 * Пример результата: array('TRC' => 'Тростянец Винницкая обл.')
	 * @return array(string => string)
	 */
	private function getLocations() {
		if (!isset($this->{__METHOD__})) {
			/**
			[
				 {
					 "id":"TRC", "label":"Тростянец Винницкая обл.", "value":"Тростянец Винницкая обл.", "adress":"ул. Ленина, 25 тел. (067) 619 32 67"
				 }, {
					 "id":"VNF", "label":"Винница Фрунзе", "value":"Винница Фрунзе", "adress":"ул. Фрунзе, 4д тел. (067) 619 13 80"
				 }, {
					 "id":"VNK", "label":"Винница Замостянская (до 30 кг за 1 место)", "value":"Винница Замостянская (до 30 кг за 1 место)", "adress":"ул. Киевская, 18/1 тел. 067 619 78 37"
				 }, {
					 "id":"VIN", "label":"Винница", "value":"Винница", "adress":"ул. 600-летия, 17 г тел. 0676196990"
				 }
			 ]
			 */
			/**
			 * @var array(string => string) $result
			 * Пример: array('TRC' => 'Тростянец Винницкая обл.')
			 */
			$this->{__METHOD__} =
				array_combine(
					rm_uppercase(df_column($this->response()->json(), 'value'))
					, df_column($this->response()->json(), 'id')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__LOCATION_NAME, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__LOCATION_NAME = 'location_name';
	/**
	 * @param string $name
	 * @return string
	 */
	public static function getLocationIdByName($name) {
		return self::i(array(self::P__LOCATION_NAME => $name))->getLocationId();
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_InTime_Model_Request_Locations_ForDeliveryTime
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}