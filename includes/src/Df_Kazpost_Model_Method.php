<?php
abstract class Df_Kazpost_Model_Method extends Df_Shipping_Model_Method_Kazakhstan {
	/** @return int */
	abstract public function getTransportId();

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
	 * Делаем метод публичным, чтобы он был доступен из @see Df_Kazpost_Model_Request_Rate
	 * @override
	 * @return Df_Shipping_Model_Config_Facade
	 */
	public function getRmConfig() {return parent::getRmConfig();}

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
					->checkCountryDestinationIsKazakhstan()
					->checkRegionOriginIsNotEmpty()
					->checkRegionDestinationIsNotEmpty()
					->checkRegionalCenterOriginIsNotEmpty()
					->checkRegionalCenterDestinationIsNotEmpty()
					/**
					 * «Предельная масса поссылки 14,5 кг»
					 * «Максимальный размер 80х80х50 см»
					 * @link http://www.kazpost.kz/ru/raschet-stoimosti-pochtovyh-otpravleniy-0
					 */
					->checkWeightIsLE(14.5)
					->checkDimensionMaxIsLE(0.8)
					->checkDimensionMinIsLE(0.5)
					->checkDimensionsSumIsLE(3)
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
	protected function getCostInTenge() {return $this->getApi()->getRate();}

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
	protected function normalizeLocationName($locationName) {return $locationName;}
	
	/** @return Df_Kazpost_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Kazpost_Model_Request_Rate::i($this);
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}