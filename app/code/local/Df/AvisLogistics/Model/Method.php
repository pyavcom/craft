<?php
abstract class Df_AvisLogistics_Model_Method extends Df_Shipping_Model_Method_Kazakhstan {
	/**
	 * Делаем метод публичным, чтобы он был доступен из @see Df_AvisLogistics_Model_Request_Rate
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
			try {}
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
	protected function getCostInTenge() {return $this->getApi()->getRate();	}
	
	/** @return Df_AvisLogistics_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = null;//Df_AvisLogistics_Model_Request_Rate::i($this);
		}
		return $this->{__METHOD__};
	}
}