<?php
class Df_InTime_Model_Request_DeliveryTime extends Df_InTime_Model_Request {
	/** @return int */
	public function getResultInDays() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->date()->getNumberOfDaysBetweenTwoDates(
				$this->getDateOfDelivery(), $this->getDateOfDeparture()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(), array(
			'date' => df_dts($this->getDateOfDeparture(), self::DATE_FORMAT)
			,'from' => $this->getLocationIdOrigin()
			,'to' => $this->getLocationIdDestination()
			,'type' => $this->getDeliveryType()
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/modules/shipment/check.php';}

	/** @return Zend_Date */
	private function getDateOfDelivery() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Date(
				$this->response()->match('#Дата доставки груза: ([\d\.]+)#ui'), self::DATE_FORMAT
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Date */
	private function getDateOfDeparture() {return df()->date()->tomorrow();}

	/** @return int */
	private function getDeliveryType() {
		return
			$this->needGetCargoFromTheShopStore()
			? ($this->needDeliverCargoToTheBuyerHome() ? 1 : 4)
			: ($this->needDeliverCargoToTheBuyerHome() ? 3 : 2)
		;
	}

	/** @return string */
	private function getLocationIdDestination() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_InTime_Model_Request_Locations_ForDeliveryTime::getLocationIdByName(
					$this->getLocationNameDestination()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLocationIdOrigin() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_InTime_Model_Request_Locations_ForDeliveryTime::getLocationIdByName(
					$this->getLocationNameOrigin()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getLocationNameDestination() {
		return $this->cfg(self::P__LOCATION_NAME_DESTINATION);
	}

	/** @return int */
	private function getLocationNameOrigin() {return $this->cfg(self::P__LOCATION_NAME_ORIGIN);}

	/** @return bool */
	private function needDeliverCargoToTheBuyerHome() {
		return $this->cfg(self::P__NEED_DELIVER_CARGO_TO_THE_BUYER_HOME);
	}

	/** @return bool */
	private function needGetCargoFromTheShopStore() {
		return $this->cfg(self::P__NEED_GET_CARGO_FROM_THE_SHOP_STORE);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__LOCATION_NAME_DESTINATION, self::V_STRING_NE)
			->_prop(self::P__LOCATION_NAME_ORIGIN, self::V_STRING_NE)
			->_prop(self::P__NEED_DELIVER_CARGO_TO_THE_BUYER_HOME, self::V_BOOL)
			->_prop(self::P__NEED_GET_CARGO_FROM_THE_SHOP_STORE, self::V_BOOL)
		;
	}
	const _CLASS = __CLASS__;
	const DATE_FORMAT = 'dd.MM.yyyy';
	const P__LOCATION_NAME_DESTINATION = 'location_name_destination';
	const P__LOCATION_NAME_ORIGIN = 'location_name_origin';
	const P__NEED_DELIVER_CARGO_TO_THE_BUYER_HOME = 'need_deliver_cargo_to_the_buyer_home';
	const P__NEED_GET_CARGO_FROM_THE_SHOP_STORE = 'need_get_cargo_from_the_shop_store';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_InTime_Model_Request_DeliveryTime
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}