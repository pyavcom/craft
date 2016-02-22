<?php
class Df_NovaPoshta_Model_Method_ToPointOfIssue extends Df_NovaPoshta_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'to-point-of-issue';
	}

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = parent::isApplicable();
		/**
		 * Вроде бы теперь этот код не нужен
		 */
		//		if ($result) {
		//			try {
		//				if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
		//					if (30.0 < $this->getRequest()->getWeightInKilogrammes()) {
		//						df_error(
		//							'Этот тариф доставки недоступен, потому что вес груза больше 30 кг.'
		//						);
		//					}
		//				}
		//				else {
		//					if (30.0 > $this->getRequest()->getWeightInKilogrammes()) {
		//						df_error(
		//							'Этот тариф доставки недоступен, потому что вес груза меньше 30 кг.'
		//						);
		//					}
		//				}
		//			}
		//			catch(Exception $e) {
		//				if ($this->needDisplayDiagnosticMessages()) {
		//					throw $e;
		//				}
		//				else {
		//					$result = false;
		//				}
		//			}
		//		}
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needDeliverToHome() {
		return false;
	}

	const _CLASS = __CLASS__;
}