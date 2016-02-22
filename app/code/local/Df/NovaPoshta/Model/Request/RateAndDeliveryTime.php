<?php
class Df_NovaPoshta_Model_Request_RateAndDeliveryTime extends Df_NovaPoshta_Model_Request {
	/** @return int */
	public function getDeliveryTime() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $deliveryDateAsString */
			$deliveryDateAsString = $this->response()->json('date/value');
			df_assert_string($deliveryDateAsString);
			/** @var Zend_Date $deliveryDate */
			$deliveryDate = new Zend_Date($deliveryDateAsString, self::DATE_FORMAT);
			$this->{__METHOD__} =
				df()->date()->getNumberOfDaysBetweenTwoDates($deliveryDate, Zend_Date::now())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getRate() {return rm_float($this->response()->json('totalPrice/value'));}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Accept' => 'application/json, text/javascript, */*; q=0.01'
			,'Cache-Control' => 'no-cache'
			,'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
			,'Pragma' => 'no-cache'
			,'X-Requested-With' => 'XMLHttpRequest'
		));
	}

	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(), array(
			'loadType_ID' => 1
			,'order_date_str' => df_dts(Zend_Date::now(), self::DATE_FORMAT)
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}

	const _CLASS = __CLASS__;
	const DATE_FORMAT = 'dd.MM.yyyy';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_NovaPoshta_Model_Request_RateAndDeliveryTime
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}