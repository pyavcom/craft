<?php
class Df_Kazpost_Model_Request_Rate extends Df_Shipping_Model_Request {
	/** @return float */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_float(str_replace('Стоимость(тенге): ', '', $this->response()->text()))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Kazpost_Model_Method */
	protected function getMethod() {return $this->cfg(self::P__METHOD);}

	/**
	 * @override
	 * @return bool
	 */
	protected function needConvertResponseFrom1251ToUtf8() {return true;}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'kazpost.kz';}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getQueryParams() {
		return array(
			'from' => $this->getMethod()->getLocationIdOrigin()
			,'obcen' => rm_01(0 < $this->getDeclaredValue())
			,'obcentenge' => $this->getDeclaredValue()
			,'to' => $this->getMethod()->getLocationIdDestination()
			,'v' => $this->getMethod()->getTransportId()
			,'w' => $this->getWeightInKilogrammes()
			,'w2' => ''
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/calc/cost.php';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}

	/** @return int */
	private function getDeclaredValue() {
		return rm_nat0($this->getRateRequest()->getDeclaredValueInTenge());
	}

	/** @return Df_Shipping_Model_Rate_Request */
	private function getRateRequest() {return $this->getMethod()->getRequest();}

	/** @return Df_Shipping_Model_Config_Facade */
	private function getRmConfig() {return $this->getMethod()->getRmConfig();}
	
	/** @return float */
	private function getWeightInKilogrammes() {
		if (!isset($this->{__METHOD__})) {
			/** @var float $result */
			$result = $this->getRateRequest()->getWeightInKilogrammes();
			if ($result <= 11) {
				// округляем до полукилограмма в большую сторону
				$result = 0.5 * ceil(2 * $result);
			}
			else {
				$result = ($result < 12) ? 11.0 : 12.0;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__METHOD, Df_Kazpost_Model_Method::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__METHOD = 'method';
	/**
	 * @static
	 * @param Df_Kazpost_Model_Method $method
	 * @return Df_Kazpost_Model_Request_Rate
	 */
	public static function i(Df_Kazpost_Model_Method $method) {
		return new self(array(self::P__METHOD => $method));
	}
}