<?php
class Df_Core_Model_Format_Html_Select extends Df_Core_Model_Abstract {
	/** @return string */
	public function render() {
		/** @var string $result */
		$result =
			Df_Core_Model_Format_Html_Tag::output(
				$this->getOptionsAsHtml()
				,'select'
				,$this->getAttributes()
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return array */
	private function getAttributes() {
		return $this->cfg(self::P__ATTRIBUTES, array());
	}

	/** @return array */
	private function getOptions() {
		return $this->cfg(self::P__OPTIONS);
	}

	/** @return array */
	private function getOptionsAsArrayOfOptionHtml() {
		/** @var array $result */
		$result = array();
		foreach ($this->getOptions() as $optionValue => $optionLabel) {
			/** @var string $optionValue */
			/** @var string $optionLabel */

			/** @var array $attributes */
			$attributes =
				array(
					'value' => df_text()->escapeHtml($optionValue)
				)
			;
			df_assert_array($attributes);
			if ($this->getSelected() === $optionValue) {
				$attributes['selected'] = 'selected';
			}
			$result[]=
				Df_Core_Model_Format_Html_Tag::output(
					df_text()->escapeHtml($optionLabel)
					,'option'
					,$attributes
				)
			;
		}
		return $result;
	}

	/** @return string */
	private function getOptionsAsHtml() {
		return df_tab_multiline(implode(Df_Core_Const::T_NEW_LINE, $this->getOptionsAsArrayOfOptionHtml()));
	}

	/** @return string|null */
	private function getSelected() {return $this->cfg(self::P__SELECTED);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ATTRIBUTES, self::V_ARRAY, false)
			->_prop(self::P__OPTIONS, self::V_ARRAY)
			->_prop(self::P__SELECTED, self::V_STRING, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__ATTRIBUTES = 'attributes';
	const P__OPTIONS = 'options';
	const P__SELECTED = 'selected';

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Format_Html_Select
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}