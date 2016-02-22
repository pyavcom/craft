<?php
class Df_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard
	extends Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard {
	/**
	 * @override
	 * @param $entityType
	 * @return array|mixed
	 */
	public function getMappings($entityType) {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded =
					df_enabled(Df_Core_Feature::DATAFLOW)
				&&
					df_cfg()->dataflow()->patches()->fixFieldMappingGui()
			;
		}
		$result =
			$patchNeeded
			?$this->getMappingsDf($entityType)
			:parent::getMappings($entityType)
		;
		return $result;
	}

	/**
	 * @param $entityType
	 * @return array
	 */
	private function getMappingsDf($entityType) {
		/** @var array $mappings */
		$mappings = parent::getMappings($entityType);
		df_assert_array($mappings);
		/** @var array $result */
		$result = array();
		foreach ($mappings as $ordering => $fieldName) {
			/** @var int $ordering */
			df_assert_integer($ordering);
			/** @var string $fieldName */
			df_assert_string_not_empty($fieldName);
			/** @var string|null $valueInFile */
			$valueInFile = $this->getValue('gui_data/map/'.$entityType.'/file/'.$ordering);
			if ($valueInFile) {
				$result[$ordering] = $fieldName;
			}
		}
		return $result;
	}

}