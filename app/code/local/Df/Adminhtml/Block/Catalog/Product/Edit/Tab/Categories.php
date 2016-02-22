<?php
class Df_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories {
	/**
	 * @override
	 * @param Varien_Data_Tree_Node $node
	 * @param int $level
	 * @return array
	 */
	protected function _getNodeJson($node, $level=1)
	{
		$result = parent::_getNodeJson($node, $level);
		if (
				df_module_enabled(Df_Core_Module::ACCESS_CONTROL)
			&&
				df_cfg()->admin()->access_control()->getEnabled()
			&&
				df_h()->accessControl()->getCurrentRole()->isModuleEnabled()
		) {
			if (
				!in_array(
					df_a($result, 'id')
					,df_h()->accessControl()->getCurrentRole()->getCategoryIds()
				)
			) {
				$result['disabled'] = true;
			}
		}
		return $result;
	}

}