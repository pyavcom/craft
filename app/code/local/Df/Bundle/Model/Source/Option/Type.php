<?php
class Df_Bundle_Model_Source_Option_Type extends Mage_Bundle_Model_Source_Option_Type {
	/** @return string[][] */
	public function toOptionArray() {
		/** @var string[][] $result */
		$result = parent::toOptionArray();
		foreach ($result as &$item) {
			/** @var string[] $item */
			df_assert_array($item);
			/** @var string $label */
			$label = df_a($item, 'label');
			df_assert_string($label);
			$item['label'] = df_mage()->bundleHelper()->__($label);
		}
		return $result;
	}
}