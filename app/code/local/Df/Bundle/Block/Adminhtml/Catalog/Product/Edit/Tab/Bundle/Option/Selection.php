<?php
class Df_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection
	extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection {
	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		return df_concat(
			parent::_toHtml()
			,"<script type='text/javascript'>
				jQuery(window).trigger ('bundle.product.edit.bundle.option.selection');
			</script>"
		);
	}
}