<?php
class Df_Sales_Block_Order_View extends Mage_Sales_Block_Order_View {
	/**
	 * @override
	 * @param mixed $data
	 * @param array $allowedTags[optional]
	 * @return string
	 */
	public function escapeHtml($data, $allowedTags = null) {
		if (
				df_enabled(Df_Core_Feature::SALES)
			&&
				df_cfg()->sales()->orderComments()->preserveLineBreaksInCustomerAccount()
		) {
			if (is_null($allowedTags)) {
				$allowedTags = array();
			}

			$allowedTags =
				rm_array_unique_fast(
					array_merge(
						$allowedTags
						,array('br')
					)
				)
			;
			$data = nl2br($data);
		}


		/** @var string $result */
		$result =
			df_text()->escapeHtml($data, $allowedTags)
		;
		df_result_string($result);
		return $result;
	}

}