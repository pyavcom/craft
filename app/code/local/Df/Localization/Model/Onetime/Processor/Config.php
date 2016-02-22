<?php
class Df_Localization_Model_Onetime_Processor_Config extends Df_Core_Model_Abstract {
	/** @return void */
	public function process() {
		if ($this->getEntry()->isLowLevel()) {
			Df_Core_Model_Resource_Config::s()->updateByPathLowLevel(
				$this->getEntry()->getPath()
				, $this->getEntry()->getValue()
				, $this->getEntry()->getValueOriginal()
			);
		}
		else {
			// Обновляем уже присутствующие в БД значения
			Df_Core_Model_Resource_Config::s()->updateByPath(
				$this->getEntry()->getPath()
				, $this->getEntry()->getValue()
				, $this->getEntry()->getValueOriginal()
			);
			if ($this->getEntry()->needSetAsDefault()) {
				/**
				 * Добавляем значения по умолчанию,
				 * если в конфигурации указан ключ set_as_default, например:
						<entry>
							<path>df_localization/frontend/hide_decimals</path>
							<value>1</value>
							<set_as_default/>
						</entry>
				 */
				Df_Core_Model_Config_Data::saveInDefaultScope(
					$this->getEntry()->getPath(), $this->getEntry()->getValue()
				);
			}
		}
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Config_Entry */
	private function getEntry() {return $this->cfg(self::$P__ENTRY);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTRY, Df_Localization_Model_Onetime_Dictionary_Config_Entry::_CLASS);
	}
	/** @var string */
	protected static $P__ENTRY = 'entry';

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary_Config_Entry $entry
	 * @return Df_Localization_Model_Onetime_Processor_Rule
	 */
	public static function i(Df_Localization_Model_Onetime_Dictionary_Config_Entry $entry) {
		return new self(array(self::$P__ENTRY => $entry));
	}
}