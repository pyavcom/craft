<?php
class Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency 
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency {
	/**
	 * @override
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row) {
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
			?$this->renderDf($row)
			:parent::render($row)
		;
	}

	/**
	 * @param Varien_Object $row
	 * @return string
	 */
	private function renderDf(Varien_Object $row) {
		$data = $row->getData($this->getColumn()->getIndex());
		if ($data) {
			$currency_code = $this->_getCurrencyCode($row);
			if (!$currency_code) {
				return $data;
			}
			$data = rm_float($data) * $this->_getRate($row);
			$sign = (!!$this->getColumn()->getShowNumberSign()) && (0 < $data) ? '+' : '';
			$data = rm_sprintf('%f', $data);
			$data =
				df_zf_currency($currency_code)->toCurrency(
					$data, array('precision' => rm_currency()->getPrecision())
				)
			;
			return $sign . $data;
		}
		return $this->getColumn()->getDefault();
	}

	const _CLASS = __CLASS__;
}