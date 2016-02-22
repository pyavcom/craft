<?php
abstract class Df_Exline_Model_Method extends Df_Shipping_Model_Method_Kazakhstan {
	/** @return string */
	abstract public function getUrgency();

	/**
	 * Делаем метод публичным, чтобы он был доступен из @see Df_Exline_Model_Request_Rate
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
					 * @link http://www.exline.kz/ru/raschet-stoimosti-pochtovyh-otpravleniy-0
					 */
					->checkWeightIsLE(14.5)
					->checkDimensionMaxIsLE(2.3)
					->checkDimensionMinIsLE(1.1)
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

	/** @return Df_Exline_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Exline_Model_Request_Rate::i($this);
		}
		return $this->{__METHOD__};
	}
}