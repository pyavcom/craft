<?php
class Df_Localization_Model_Onetime_Type_Page extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Registry_Collection_Cms_Pages
	 */
	public function getAllEntities() {return df_h()->dataflow()->registry()->cmsPages();}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClassSuffix() {return 'Cms_Page';}
}