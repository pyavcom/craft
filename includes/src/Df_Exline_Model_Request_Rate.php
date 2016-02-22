<?php
class Df_Exline_Model_Request_Rate extends Df_Shipping_Model_Request {
	/** @return float */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			if ($this->response()->json('origin')) {
				$this->getRateRequest()->throwExceptionInvalidOrigin();
			}
			if ($this->response()->json('destination')) {
				$this->getRateRequest()->throwExceptionInvalidDestination();
			}
			$this->{__METHOD__} =
					rm_float($this->response()->json('price'))
				+
					rm_float($this->response()->json('fee'))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Exline_Model_Method */
	protected function getMethod() {return $this->cfg(self::P__METHOD);}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'calc.exline.kz';}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getQueryParams() {
		return array(
			'origin' => $this->getLocator()->getLocationIdOrigin()
			,'destination' => $this->getLocator()->getLocationIdDestination()
			,'weight' => rm_sprintf('%.1f', $this->getWeightVolumetric())
			,'urgency' => $this->getMethod()->getUrgency()
			,'declared_value' => $this->getDeclaredValue()
			,'type' => 'parcel'
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/calculate.json';}

	/** @return Df_Exline_Model_Locator */
	private function getLocator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Exline_Model_Locator::i($this->getRateRequest());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}

	/** @return int */
	private function getDeclaredValue() {return rm_nat0($this->getRateRequest()->getDeclaredValueInTenge());}

	/** @return Df_Shipping_Model_Rate_Request */
	private function getRateRequest() {return $this->getMethod()->getRequest();}

	/** @return Df_Shipping_Model_Config_Facade */
	private function getRmConfig() {return $this->getMethod()->getRmConfig();}
	
	/** @return float */
	private function getWeightVolumetric() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				max(
					$this->getRateRequest()->getWeightInKilogrammes()
					,$this->getRateRequest()->getVolumeBoxInCubicCentimeters() / 6000.0
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
		$this->_prop(self::P__METHOD, Df_Exline_Model_Method::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__METHOD = 'method';
	/**
	 * @static
	 * @param Df_Exline_Model_Method $method
	 * @return Df_Exline_Model_Request_Rate
	 */
	public static function i(Df_Exline_Model_Method $method) {
		return new self(array(self::P__METHOD => $method));
	}
}