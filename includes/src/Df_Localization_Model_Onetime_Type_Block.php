<?php
class Df_Localization_Model_Onetime_Type_Block extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Registry_Collection_Cms_Blocks
	 */
	public function getAllEntities() {return df_h()->dataflow()->registry()->cmsBlocks();}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClassSuffix() {return 'Cms_Block';}
}