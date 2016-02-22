<?php
class Df_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default {
	/**
	 * @override
	 * @param $basePrice
	 * @param $price
	 * @param bool $strong
	 * @param string $separator
	 * @return string
	 */
	public function displayPrices($basePrice, $price, $strong = false, $separator = '<br />') {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded =
					df_enabled(Df_Core_Feature::LOCALIZATION)
				&&
					(
						df_is_admin()
						? df_cfg()->localization()->translation()->admin()->needHideDecimals()
						: df_cfg()->localization()->translation()->frontend()->needHideDecimals()
					)
			;
		}
		return
			$patchNeeded
			?$this->displayPricesDf($basePrice, $price, $strong, $separator)
			:parent::displayPrices($basePrice, $price, $strong, $separator)
		;
	}

	/**
	 * @param $basePrice
	 * @param $price
	 * @param bool $strong
	 * @param string $separator
	 * @return string
	 */
	private function displayPricesDf($basePrice, $price, $strong = false, $separator = '<br />')
	{
		return
			$this->displayRoundedPrices(
				$basePrice
				,$price
				,rm_currency()->getPrecision()
				,$strong
				,$separator
			)
		;
	}

}