<?php
class Df_Exline_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return bool
	 * @link https://api.exline.kz/tracking
	 */
	public function isTrackingAvailable() {return true;}
}