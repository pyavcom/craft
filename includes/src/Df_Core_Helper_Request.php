<?php
class Df_Core_Helper_Request extends Mage_Core_Helper_Abstract {
	/**
	 * @param array $array
	 * @param array $dateFields
	 * @return array
	 */
	public function filterDates($array, $dateFields) {
		if (!empty($dateFields)) {
			$filterInput =
				new Zend_Filter_LocalizedToNormalized (
					array(
						'date_format' =>
							Mage::app()->getLocale()->getDateFormat(
								Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
							)
						)
					)
			;
			$filterInternal =
				new Zend_Filter_NormalizedToLocalized (
					array(
						'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
					)
				)
			;
			foreach ($dateFields as $dateField) {
				if (array_key_exists($dateField, $array) && !empty($dateField)) {
					$array[$dateField] = $filterInput->filter($array[$dateField]);
					$array[$dateField] = $filterInternal->filter($array[$dateField]);
				}
			}
		}
		return $array;
	}

	/**
	 * @param string $paramName
	 * @param mixed $default[optional]
	 * @return mixed
	 */
	public function getParam($paramName, $default = null) {
		return Mage::app()->getRequest()->getParam($paramName, $default);
	}

	/** @return Df_Core_Helper_Request */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}