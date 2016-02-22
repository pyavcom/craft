<?php
abstract class Df_NovaPoshta_Model_Method extends Df_Shipping_Model_Method_Ukraine {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract protected function needDeliverToHome();

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		/** @var string $result */
		$result = parent::getMethodTitle();
		if (!is_null($this->getRequest()) && (0 !== $this->getApi()->getDeliveryTime())) {
			$result =
				implode(
					' '
					,array(
						rm_sprintf('%s:', $result)
						,rm_sprintf(
							'%s'
							,$this->formatTimeOfDelivery(
								$this->getApi()->getDeliveryTime()
							)
						)
					)
				)
			;
		}
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = parent::isApplicable();
		if ($result) {
			try {
				$this
					->checkCountryOriginIsUkraine()
					->checkCountryDestinationIsUkraine()
					->checkCityOriginIsNotEmpty()
					->checkCityDestinationIsNotEmpty()
				;
				if (!$this->getLocationIdOrigin()) {
					$this->throwExceptionInvalidOrigin();
				}
				if (!$this->getLocationIdDestination()) {
					$this->throwExceptionInvalidDestination();
				}
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @return float
	 */
	protected function getCostInHryvnias() {return $this->getApi()->getRate();}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {
		return Df_NovaPoshta_Model_Request_Locations::s()->getLocations();
	}

	/** @return Df_NovaPoshta_Model_Request_RateAndDeliveryTime */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_NovaPoshta_Model_Request_RateAndDeliveryTime::i($this->getPostParams())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getDeliveryType() {
		/** @var int $result */
		$result = null;
		if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
			$result =
					$this->needDeliverToHome()
				?
					1
				:
					2
			;
		}
		else {
			$result =
					$this->needDeliverToHome()
				?
					3
				:
					4
			;
		}
		df_result_integer($result);
		return $result;
	}

	/** @return array(string => string|int|float) */
	private function getPostParams() {
		return array(
			'deliveryType_id' => $this->getDeliveryType()
			,'mass' => $this->getRequest()->getWeightInKilogrammes()
			,'publicPrice' =>
				/**
				 * Минимальная объявленная стоимость — 400 гривен
				 * @link http://novaposhta.ua/frontend/calculator/ru
				 */
				min(400, $this->getRequest()->getDeclaredValueInHryvnias())
			,'recipientCity' => df_text()->ucfirst($this->getRequest()->getDestinationCity())
			,'recipient_city_id' => $this->getLocationIdDestination()
			,'sender_city' => df_text()->ucfirst($this->getRequest()->getOriginCity())
			,'sender_city_id' => $this->getLocationIdOrigin()
			,'vWeight' =>
				/**
				 * @link http://novaposhta.ua/delivery/answers?lang=ru
				 */
				250 * $this->getRequest()->getVolumeInCubicMetres()
		);
	}

	const _CLASS = __CLASS__;
}