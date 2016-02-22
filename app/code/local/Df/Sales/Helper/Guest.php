<?php
class Df_Sales_Helper_Guest extends Mage_Sales_Helper_Guest {
	/**
	 * Get Breadcrumbs for current controller action
	 *
	 * @param Mage_Core_Controller_Front_Action $controller
	 */
	public function getBreadcrumbs($controller)
	{
		/** @var Mage_Page_Block_Html_Breadcrumbs $breadcrumbs */
		$breadcrumbs = $controller->getLayout()->getBlock('breadcrumbs');
		/**
		 * Magento CE не делает эту проверку, и иногда происходит сбой:
		 * Fatal error: Call to a member function addCrumb() on a non-object
		 */
		if ($breadcrumbs instanceof Mage_Page_Block_Html_Breadcrumbs) {
			$breadcrumbs
				->addCrumb(
					'home'
					,array(
						'label' => df_mage()->cmsHelper()->__('Home')
						,'title' => df_mage()->cmsHelper()->__('Go to Home Page')
						,'link'  => Mage::getBaseUrl()
					)
				)
				->addCrumb(
					'cms_page'
					,array(
						// BEGIN PATCH
						'label' => df_mage()->salesHelper()->__('Order Information')
						,'title' => df_mage()->salesHelper()->__('Order Information')
						// END PATCH
					)
			);
		}
	}
}