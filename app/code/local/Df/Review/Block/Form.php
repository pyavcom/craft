<?php
class Df_Review_Block_Form extends Mage_Review_Block_Form {
	/**
	 * В родительском конструкторе @see Mage_Review_Block_Form::___construct()
	 * программисты ошиблись (описались):
	 * там происходит вызов $this->getAllowWriteReviewFlag вместо $this->getAllowWriteReviewFlag().
	 * @override
	 * @param bool $value
	 * @return Df_Review_Block_Form
	 */
	public function setAllowWriteReviewFlag($value) {
		$this->setData('allow_write_review_flag', $value);
		$this->getAllowWriteReviewFlag = $value;
	}
	/** @var bool */
	public $getAllowWriteReviewFlag = null;
}