<?php
class Df_1C_Model_Cml2_Action_Init extends Df_1C_Model_Cml2_Action {
	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		$this->setResponseBodyAsArrayOfStrings(
			array(
				/**
				 * @todo надо добавить поддержку формата ZIP
				 */
				$this->implodeResponseParam('zip', 'no')
				,$this->implodeResponseParam('file_limit', -1)
				,''
			)
		);
	}

	/**
	 * @param string $paramName
	 * @param string|int $paramValue
	 * @return string
	 */
	private function implodeResponseParam($paramName, $paramValue) {
		df_param_string_not_empty($paramName, 0);
		if (!is_int($paramValue)) {
			df_param_string($paramValue, 1);
		}
		return implode('=', array($paramName, $paramValue));
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Init
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}