<?php
class Df_Localization_Model_Onetime_Action extends Df_Core_Model_Controller_Action_Admin {
	/**
	 * @override
	 * @return string
	 */
	protected function generateResponseBody() {
		try {
			$this->getProcessor()->process();
			rm_session()->addSuccess(sprintf(
				'Оформительская тема «%s» успешно русифицирована.'
				, $this->getProcessor()->getTitle()
			));
			Mage::getConfig()->saveConfig($this->getConfigPath(), df_dts());
			Mage::getConfig()->reinit();
			rm_cache_clean();
		}
		catch (Exception $e) {
			rm_exception_to_session($e);
			df_notify_exception($e);
		}
		$this->getController()->redirectReferer();
		return '';
	}

	/** @return string */
	private function getConfigPath() {
		return Df_Localization_Model_Onetime_Processor::getConfigPath_TimeOfLastProcessing(
			$this->getProcessorId()
		);
	}

	/** @return Df_Localization_Model_Onetime_Processor */
	private function getProcessor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Onetime_Processor_Collection::s()
					->getItemById($this->getProcessorId())
			;
			df_assert($this->{__METHOD__} instanceof Df_Localization_Model_Onetime_Processor);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getProcessorId() {return df_request(self::RP__PROCESSOR);}

	const RP__PROCESSOR = 'processor';
	/**
	 * @static
	 * @param Df_Localization_ThemeController $controller
	 * @return Df_Localization_Model_Onetime_Action
	 */
	public static function i(Df_Localization_ThemeController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}