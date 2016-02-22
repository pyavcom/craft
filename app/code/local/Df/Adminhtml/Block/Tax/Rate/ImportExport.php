<?php
class Df_Adminhtml_Block_Tax_Rate_ImportExport extends Mage_Adminhtml_Block_Tax_Rate_ImportExport {
	// здесь не надо перекрывать метод __


	/**
	 * Create button and return its html
	 * @override
	 * @param string $label
	 * @param string $onclick
	 * @param string $class
	 * @param string $id
	 * @return string
	 */
	public function getButtonHtml($label, $onclick, $class='', $id=null) {
		/** @var string $result */
		$result =
			parent::getButtonHtml(
				$this->__($label)
				,$onclick
				,$class
				,$id
			)
		;
		df_result_string($result);
		return $result;
	}
}