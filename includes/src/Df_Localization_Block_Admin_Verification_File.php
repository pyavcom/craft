<?php
class Df_Localization_Block_Admin_Verification_File extends Df_Core_Block_Admin {
	/** @return string */
	public function getName() {return $this->getFile()->getName();}

	/** @return int */
	public function getNumAbsentEntries() {return $this->getFile()->getNumAbsentEntries();}

	/** @return int */
	public function getNumEntries() {return $this->getFile()->getNumEntries();}

	/** @return int */
	public function getNumUntranslatedEntries() {return $this->getFile()->getNumUntranslatedEntries();}

	/** @return bool */
	public function isFullyTranslated() {return $this->getFile()->isFullyTranslated();}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return self::DEFAULT_TEMPLATE;}

	/** @return Df_Localization_Model_Translation_File */
	private function getFile() {return $this->cfg(self::P__FILE);}

	const DEFAULT_TEMPLATE = 'df/localization/verification/file.phtml';
	const P__FILE = 'file';

	/**
	 * @param Df_Localization_Model_Translation_File $file
	 * @return Dfa_LicensorGenerator_Block_License
	 */
	public static function i(Df_Localization_Model_Translation_File $file) {
		return df_block(new self(array(self::P__FILE => $file)));
	}
}