<?php
abstract class Df_AvisLogistics_Model_Request extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {
		return 'www.avislogistics.kz';
	}
}

