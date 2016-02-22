<?php
class Df_Core_Model_InputRequest extends Df_Core_Model_Abstract {
	/**
	 * @param string $paramName
	 * @param string $defaultValue[optional]
	 * @return string|null
	 */
	public function getParam($paramName, $defaultValue = null) {
		/** @var string|null $result */
		$result = $this->getRequest()->getParam($paramName, $defaultValue);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return Mage_Core_Controller_Request_Http */
	protected function getRequest() {
		return $this->cfg(self::P__REQUEST);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__REQUEST, 'Mage_Core_Controller_Request_Http');
	}
	/** Используется из @see Df_Core_Model_Controller_Action::getRmRequestClass() */
	const _CLASS = __CLASS__;
	const P__REQUEST = 'request';
}