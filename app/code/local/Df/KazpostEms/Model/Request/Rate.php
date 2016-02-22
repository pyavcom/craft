<?php
abstract class Df_KazpostEms_Model_Request_Rate extends Df_Shipping_Model_Request {
	/** @return float */
	abstract protected function getTaxes();

	/**
	 * @override
	 * @return float
	 */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			$result = rm_float($this->response()->pq('td:last')->text());
			if (0.0 === $result) {
				df_notify(
					"Сервер emscal.kazpost.kz вернул непредусмотренный ответ."
					. "\r\nПараметры запроса:\r\n%s"
					. "\r\nОтвет сервера:\r\n«%s»."
					, rm_print_params($this->getPostParameters())
					, $this->response()->text()
				);
			}
			$result += $this->getTaxes();
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_KazpostEms_Model_Method */
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
	protected function getQueryHost() {return 'emscal.kazpost.kz';}

	/** @return Df_Shipping_Model_Rate_Request */
	protected function getRateRequest() {return $this->getMethod()->getRequest();}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}
	
	/** @return int */
	protected function getWeightId() {
		if (!isset($this->{__METHOD__})) {
			$this->getWeightInKilogrammes();
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	protected function getWeightInKilogrammes() {
		if (!isset($this->{__METHOD__})) {
			/** @var float $result */
			$result = $this->getRateRequest()->getWeightInKilogrammes();
			/** @var int $weightId */
			if ($result <= 0.15) {
				$result = 0.15;
				$weightId = 1;
			}
			else if ($result < 0.3) {
				$result = 0.3;
				$weightId = 2;
			}
			else {
				// округляем до полукилограмма в большую сторону
				$result = 0.5 * ceil(2 * $result);
				// Калькулятор не содержит шага 9.5
				if (9.5 === $result) {
					$result = 10.0;
				}
				$weightId = 2 + rm_round(2 * $result);
			}
			$this->{__METHOD__} = $result;
			$this->{__CLASS__ . '::getWeightId'} = $weightId;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__METHOD, Df_KazpostEms_Model_Method::_CLASS);
	}
	const P__METHOD = 'method';
	/**
	 * @static
	 * @param string $class
	 * @param Df_KazpostEms_Model_Method $method
	 * @return Df_KazpostEms_Model_Request_Rate
	 */
	public static function i($class, Df_KazpostEms_Model_Method $method) {
		df_param_string_not_empty($class, 0);
		/** @var Df_KazpostEms_Model_Request_Rate $result */
		$result = new $class(array(self::P__METHOD => $method));
		df_assert($result instanceof Df_KazpostEms_Model_Request_Rate);
		return $result;
	}
}