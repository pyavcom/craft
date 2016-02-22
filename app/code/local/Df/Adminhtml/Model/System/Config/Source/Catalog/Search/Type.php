<?php
class Df_Adminhtml_Model_System_Config_Source_Catalog_Search_Type
	extends  Mage_Adminhtml_Model_System_Config_Source_Catalog_Search_Type {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		return array_map(array($this, 'translate'), parent::toOptionArray());
	}

	/**
	 * @param array $option
	 * @return array
	 */
	public function translate(array $option) {
		/** @var array $result */
		$result =
			array_merge(
				$option
				,array(
					Df_Admin_Model_Config_Source::OPTION_KEY__LABEL =>
						df_h()->catalogSearch()->__(
							df_a($option, Df_Admin_Model_Config_Source::OPTION_KEY__LABEL)
						)
				)
			)
		;
		df_result_array($result);
		return $result;
	}

}