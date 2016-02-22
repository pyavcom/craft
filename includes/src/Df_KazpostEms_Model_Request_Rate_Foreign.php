<?php
class Df_KazpostEms_Model_Request_Rate_Foreign extends Df_KazpostEms_Model_Request_Rate {
	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getPostParameters() {
		return array(
			'countryID' => $this->getCountryId()
			,'czone' => $this->getCountryZone()
			,'kurs' => ''
			,'tsend' => 2 // посылка
			,'vesm' => ''
			,'wID' => $this->getWeightId()
			,'weightm' => $this->getWeightInKilogrammes()
		);
	}

	/**
	 * @override
	 * @return float
	 */
	protected function getTaxes() {
		/**
		 * экспедиционный сбор независимо от  веса и объявленной ценности: 310 тенге
		 * плата за каждый тенге объявленной ценности: 10%
		 * @link http://www.kazpost.kz/downloads/otchet2012/korp_doc/12.xls
		 */
		return
			(0 === $this->getRateRequest()->getDeclaredValueInTenge())
			? 0.0
			: 310 + 0.1 * $this->getRateRequest()->getDeclaredValueInTenge()
		;
	}

	/** @return int */
	private function getCountryId() {
		return
			$this->getRateRequest()->isDestinationMoscow()
			? 150
			: Df_KazpostEms_Model_Request_Countries::s()->getCountryId($this->getDestinationCountry())
		;
	}

	/** @return int */
	private function getCountryZone() {
		return
			$this->getRateRequest()->isDestinationMoscow()
			? 1
			: Df_KazpostEms_Model_Request_Countries::s()->getZone($this->getDestinationCountry())
		;
	}

	/** @return Df_Directory_Model_Country */
	private function getDestinationCountry() {
		return $this->getRateRequest()->getDestinationCountry();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/inc/result_m.php';}

	const _CLASS = __CLASS__;
}