<?php
class Df_AvisLogistics_Model_Request_Rate_Domestic extends Df_AvisLogistics_Model_Request_Rate {
	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getPostParameters() {
		return array(
			'from_city' => $this->getMethod()->getLocationIdOrigin()
			,'notif' => 80 // Уведомление путем сообщения по городскому телефону
			,'to_city' => $this->getMethod()->getLocationIdDestination()
			,'tsend' => 2  // посылка
			,'ves' => ''
			,'wID' => $this->getWeightId()
			,'weight' => $this->getWeightInKilogrammes()
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/inc/result_kz.php';}

	/**
	 * @override
	 * @return float
	 */
	protected function getTaxes() {
		/**
		 * 1% от объявленной ценности
		 * @link http://www.kazpost.kz/downloads/otchet2012/korp_doc/11.xls
		 */
		return 0.01 * $this->getRateRequest()->getDeclaredValueInTenge();
	}

	const _CLASS = __CLASS__;
}