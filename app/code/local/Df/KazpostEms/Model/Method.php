<?php
/**
 * @link http://www.kazpost.kz/ru/ekspress-dostavka-otpravleniy-ems
 */
class Df_KazpostEms_Model_Method extends Df_Shipping_Model_Method_Kazakhstan {
	/**
	 * Делаем метод публичным, чтобы он был доступен из @see Df_Kazpost_Model_Request_Rate
	 * @override
	 * @return int
	 */
	public function getLocationIdDestination() {
		return
			$this->getLocationId(
				$this->getRequest()->getDestinationRegionalCenter()
				, $isDestination = true
				, $locationIsRegion = true
			)
		;
	}

	/**
	 * Делаем метод публичным, чтобы он был доступен из @see Df_Kazpost_Model_Request_Rate
	 * @override
	 * @return int
	 */
	public function getLocationIdOrigin() {
		return
			$this->getLocationId(
				$this->getRequest()->getOriginRegionalCenter()
				, $isDestination = false
				, $locationIsRegion = true
			)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		return '';
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
				if ($this->getRequest()->isDomestic()) {
					$this
						->checkRegionOriginIsNotEmpty()
						->checkRegionDestinationIsNotEmpty()
						->checkRegionalCenterOriginIsNotEmpty()
						->checkRegionalCenterDestinationIsNotEmpty()
					;
				}
				$this
					/**
					 * «Предельный вес посылки экспресс - отправлений ЕМS составляет 20 кг.»
					 * Однако калькулятор на официальном сайте работает только с весом до 10.5 кг.
					 * @link http://www.kazpost.kz/ru/ekspress-dostavka-otpravleniy-ems
					 */
					->checkWeightIsLE(10.5)
					/**
					 * «Предельный вес посылки экспресс - отправлений ЕМS составляет 20 кг.»
					 * «Габариты экспресс-отправлений ЕМS
					 * не должны превышать 1,5 м по любому из размеров
					 * или 3 м по сумме длины»
					 * @link http://www.kazpost.kz/ru/ekspress-dostavka-otpravleniy-ems
					 */
					->checkDimensionMaxIsLE(1.5)
					->checkDimensionsSumIsLE(3)
					->checkCountryDestinationIsNotEmpty()
				;
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
	protected function getCostInTenge() {
		return $this->getApi()->getRate();
	}

	/**
	 * @override
	 * @return array(string => int)
	 */
	protected function getLocations() {
		return array(
			'Астана' => 1
			,'Актобе' => 2
			,'Актау' => 3
			,'Алматы' => 4
			,'Атырау' => 5
			,'Караганда' => 6
			,'Кызылорда' => 7
			,'Костанай' => 8
			,'Павлодар' => 9
			,'Петропавловск' => 10
			,'Тараз' => 11
			,'Усть-Каменогорск' => 12
			,'Уральск' => 13
			,'Шымкент' => 14
			,'Кокшетау' => 15
			,'Талдыкорган' => 16
		);
	}

	/**
	 * @override
	 * @param string $locationName
	 * @return string
	 */
	protected function normalizeLocationName($locationName) {
		return $locationName;
	}
	
	/** @return Df_KazpostEms_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_KazpostEms_Model_Request_Rate::i($this->getRequestClass(), $this);
		}
		return $this->{__METHOD__};
	}
	/**
	 * @override
	 * @return string
	 */
	private function getRequestClass() {
		return
			$this->getRequest()->isDomestic()
			? Df_KazpostEms_Model_Request_Rate_Domestic::_CLASS
			: Df_KazpostEms_Model_Request_Rate_Foreign::_CLASS
		;
	}

	const _CLASS = __CLASS__;
}