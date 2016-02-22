<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Row extends Df_Core_Block_Abstract_NoCache {
	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Field */
	public function getFields() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Checkout_Model_Collection_Ergonomic_Address_Field::i();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $fieldAsHtml
	 * @param string $fieldType
	 * @param int $ordering
	 * @param int $totalCount
	 * @return string
	 */
	public function wrapField($fieldAsHtml, $fieldType, $ordering, $totalCount) {
		return Df_Core_Model_Format_Html_Tag::output($fieldAsHtml, 'div', array(
			'class' => df_output()->getCssClassesAsString(array(
				'field', rm_sprintf('df-field-%s', $fieldType)
			))
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		$result =
				(0 === $this->getFields()->count())
			?
				''
			:
				Df_Core_Model_Format_Html_Tag::output(
					implode(
						"\n"
						,array_map(
							array($this, 'wrapField')
							,$this->getFields()->walk('toHtml')
							,$this->getFields()->walk('getType')
							,range(1, $this->getFields()->count())
							,df_array_fill(0, $this->getFields()->count(), $this->getFields()->count())
						)
					)
					,'li'
					,array(
						'class' => $this->getCssClassesAsText()
					)
				)
		;
		return $result;
	}

	/** @return string */
	private function getCssClassesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_output()->getCssClassesAsString(
					array(!$this->hasSingleField() ? 'fields' : 'wide')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function hasSingleField() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (1 === $this->getFields()->count());
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address_Row */
	public static function i() {return df_block(__CLASS__);}
}