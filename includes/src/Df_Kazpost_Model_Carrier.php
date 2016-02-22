<?php
class Df_Kazpost_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return bool
	 * @link http://online.kazpost.kz/ru/Tracking/Index
	 */
	public function isTrackingAvailable() {return true;}
}