<?php
class Df_Localization_Model_Onetime_Processor extends Df_Core_Model_Abstract {
	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->cfg(self::P__ID);}

	/** @return string */
	public function getLink() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_url_admin('df_localization/theme/process', array(
				Df_Localization_Model_Onetime_Action::RP__PROCESSOR => $this->getId()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getSortWeight() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = 0;
			if ($this->isThemeInstalled()) {
				$result -= 10;
				if (!$this->getTimeOfLastProcessing()) {
					$result -= 5;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Date|null */
	public function getTimeOfLastProcessing() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $resultAsString */
			$resultAsString =
				Mage::getStoreConfig(self::getConfigPath_TimeOfLastProcessing($this->getId()))
			;
			$this->{__METHOD__} = rm_n_set(!$resultAsString ? null : new Zend_Date($resultAsString));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getTitle() {return $this->cfg(self::$P__TITLE);}

	/** @return string */
	public function getType() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->isThemeInstalled()
				? self::$TYPE__ABSENT
				: ($this->getTimeOfLastProcessing() ? self::$TYPE__PROCESSED : self::$TYPE__APPLICABLE)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isApplicable() {return self::$TYPE__APPLICABLE === $this->getType();}

	/** @return bool */
	public function isProcessed() {return self::$TYPE__PROCESSED === $this->getType();}

	/** @return bool */
	public function isThemeInstalled() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Алгоритм позаимствовал из @see Mage_Core_Model_Design_Package::designPackageExists()
			 * Не использую напрямую @see Mage_Core_Model_Design_Package::designPackageExists()
			 * в целях ускорения: чтобы использовать уже готовую переменную $packageDir
			 * для расчёта папки темы.
			 */
			/** @var string $packageDir */
			$packageDir = df_concat_path(Mage::getBaseDir('design'), 'frontend', $this->getPackage());
			$this->{__METHOD__} =
					is_dir($packageDir)
				&&
					(
							!$this->getTheme()
						||
							is_dir(df_concat_path($packageDir, $this->getTheme()))
					)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	public function process() {
		$this->applyDictionary($this->getDictionaryForTheme());
		$this->applyDictionary($this->getDictionaryCommon());
		$this->additionalProcessing();
		$this->saveModifiedMagentoEntities();
		rm_cache_clean();
		Mage::getConfig()->reinit();
	}

	/** @return void */
	protected function additionalProcessing() {}

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary $dictionary
	 * @return void
	 */
	private function applyDictionary(Df_Localization_Model_Onetime_Dictionary $dictionary) {
		foreach ($dictionary->getRules() as $rule) {
			/** @var Df_Localization_Model_Onetime_Dictionary_Rule $rule */
			Df_Localization_Model_Onetime_Processor_Rule::i($rule)->process();
		}
		foreach ($dictionary->getConfigEntries() as $configEntry) {
			/** @var Df_Localization_Model_Onetime_Dictionary_Config_Entry $configEntry */
			Df_Localization_Model_Onetime_Processor_Config::i($configEntry)->process();
		}
	}

	/** @return Df_Localization_Model_Onetime_Dictionary */
	private function getDictionaryForTheme() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Onetime_Dictionary::i($this->getDictionaryLocalPath())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Onetime_Dictionary */
	private function getDictionaryCommon() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Onetime_Dictionary::i('common.xml')
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getDictionaryLocalPath() {return $this->cfg(self::$P__DICTIONARY);}

	/** @return string */
	private function getPackage() {return $this->cfg(self::$P__PACKAGE);}

	/** @return string */
	private function getTheme() {return $this->cfg(self::$P__THEME);}

	/** @return void */
	private function saveModifiedMagentoEntities() {
		Df_Localization_Model_Onetime_TypeManager::s()->saveModifiedMagentoEntities();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DICTIONARY, self::V_STRING_NE)
			->_prop(self::P__ID, self::V_STRING_NE)
			->_prop(self::$P__PACKAGE, self::V_STRING_NE)
			->_prop(self::$P__THEME, self::V_STRING)
			->_prop(self::$P__TITLE, self::V_STRING_NE)
		;
	}
	const _CLASS = __CLASS__;
	const P__ID = 'id';
	/**
	 * @param string $processorId
	 * @return string
	 */
	public static function getConfigPath_TimeOfLastProcessing($processorId) {
		return 'rm/design_theme_processor/time/' . $processorId;
	}

	/** @var string */
	private static $P__DICTIONARY = 'dictionary';
	/** @var string */
	private static $P__PACKAGE = 'package';
	/** @var string */
	private static $P__THEME = 'theme';
	/** @var string */
	private static $P__TITLE = 'title';

	/** @var string */
	private static $TYPE__ABSENT = 'absent';
	/** @var string */
	private static $TYPE__APPLICABLE = 'applicable';
	/** @var string */
	private static $TYPE__PROCESSED = 'processed';
}