<?php
/**
 * @method Df_Psbank_Model_Payment getPaymentMethod()
 * @method Df_Psbank_Model_Config_Area_Service getServiceConfig()
 */
abstract class Df_Psbank_Model_Request_Secondary extends Df_Payment_Model_Request_Secondary {
	/** @return int */
	abstract protected function getTransactionType();

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	public function getParams() {
		return array_merge($this->getParamsForSignature(), array('P_SIGN' => $this->getSignature()));
	}

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Zend_Uri::factory($this->getServiceConfig()->getUrlPaymentPage()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Psbank_Model_Request_Secondary */
	public function process() {
		/** @var Zend_Http_Client $httpClient */
		$httpClient = new Zend_Http_Client();
		$httpClient
			->setHeaders(array())
			->setUri($this->getUri())
			->setConfig(array('timeout' => 10))
			->setMethod(Zend_Http_Client::POST)
			->setParameterPost($this->getParams())
		;
		/** @var Zend_Http_Response $response */
		$response = $httpClient->request();
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {
		return $this->getResponsePayment()->getOperationExternalId();
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getResponseAsArray() {df_abstract(__METHOD__);}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {df_abstract(__METHOD__);}

	/** @return array(string => string) */
	private function getParamsForSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'AMOUNT' => $this->getResponsePayment()->getAmount()->getAsString()
				,'BACKREF' => ''
				,'CURRENCY' => 'RUB'
				,'EMAIL' => Df_Core_Helper_Mail::s()->getCurrentStoreMailAddress()
				,'INT_REF' => $this->getPaymentExternalId()
				,'NONCE' => Df_Psbank_Helper_Data::s()->generateNonce()
				,'ORDER' => $this->getOrder()->getIncrementId()
				,'ORG_AMOUNT' => $this->getResponsePayment()->getAmount()->getAsString()
				,'RRN' => $this->getResponsePayment()->getRetrievalReferenceNumber()
				,'TERMINAL' => $this->getServiceConfig()->getTerminalId()
				,'TIMESTAMP' => Df_Psbank_Helper_Data::s()->getTimestamp()
				,'TRTYPE' => $this->getTransactionType()
			);
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Psbank_Model_Response */
	private function getResponsePayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Psbank_Model_Response::i(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
					->loadFromPaymentInfo($this->getPaymentInfoInstance())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Psbank_Helper_Data::s()->generateSignature(
					$this->getParamsForSignature()
					,array(
						'ORDER', 'AMOUNT', 'CURRENCY', 'ORG_AMOUNT', 'RRN', 'INT_REF', 'TRTYPE'
						, 'TERMINAL', 'BACKREF', 'EMAIL', 'TIMESTAMP', 'NONCE'
					)
					,$this->getServiceConfig()->getRequestPassword()
				)
			;
		}
		return $this->{__METHOD__};
	}
}