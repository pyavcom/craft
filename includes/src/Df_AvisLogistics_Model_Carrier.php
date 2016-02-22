<?php
class Df_AvisLogistics_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'avis-logistics';}
	/**
	 * @override
	 * @return bool
	 * @link http://www.avislogistics.kz/rus/trace/
	 */
	public function isTrackingAvailable() {return true;}
}