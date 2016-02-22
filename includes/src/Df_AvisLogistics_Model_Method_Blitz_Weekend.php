<?php
class Df_AvisLogistics_Model_Method_Blitz_Weekend extends Df_AvisLogistics_Model_Method_Blitz {
	/**
	 * За доставку в нерабочее время взымается доплата 1000 тг к тарифу.
	 * @link http://www.avislogistics.kz/rus/dservices/
	 * @override
	 * @return float
	 */
	protected function getCostInTenge() {return 1000 + parent::getCostInTenge();}
}