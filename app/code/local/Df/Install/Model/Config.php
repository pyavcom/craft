<?php
class Df_Install_Model_Config extends Mage_Install_Model_Config {
	/** @return Varien_Object[] */
	public function getWizardSteps() {
		/** @var array $result */
		$result = parent::getWizardSteps();
		/** @var int[] $indicesToRemove */
		$indicesToRemove = array();
		foreach ($result as $index => $step) {
			/** @var int $index */
			/** @var Varien_Object $step */
			if ('true' === $step->getData('remove')) {
				$indicesToRemove[]= $index;
			}
		}
		return array_diff_key($result, array_flip ($indicesToRemove));
	}
}