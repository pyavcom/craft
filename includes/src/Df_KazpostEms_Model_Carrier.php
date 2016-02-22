<?php
class Df_KazpostEms_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'kazpost-ems';}
	/**
	 * @override
	 * @return bool
	 * @link http://online.kazpost.kz/ru/Tracking/Index
	 */
	public function isTrackingAvailable() {return true;}
}