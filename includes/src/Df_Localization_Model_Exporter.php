<?php
class Df_Localization_Model_Exporter extends Df_Core_Model_Abstract {
	/**
	 * @param Df_Localization_Model_Translation_Db_Source_Key $sourceKey
	 * @param string $stringInTargetLanguage
	 * @return Df_Localization_Model_Exporter
	 */
	public function addTranslationStringToModule(
		Df_Localization_Model_Translation_Db_Source_Key $sourceKey
		,$stringInTargetLanguage
	) {
		if (!isset($this->{__METHOD__}[$sourceKey->getModule()])) {
			$this->{__METHOD__}[$sourceKey->getModule()] = array();
		}
		$this->{__METHOD__}[$sourceKey->getModule()][$sourceKey->getString()] =
			$stringInTargetLanguage
		;
		return $this;
	}

	/**
	 * @param string $sourceKey
	 * @return Df_Localization_Model_Translation_Db_Source_Key
	 */
	public function convertSourceKeyToObject($sourceKey) {
		return Df_Localization_Model_Translation_Db_Source_Key::i($sourceKey);
	}

	/** @return Df_Localization_Model_Exporter */
	public function process() {
		$this->writeTranslationToFiles($this->getTranslationByModules());
		return $this;
	}

	/**
	 * @param string $moduleName
	 * @param array $translation
	 * @return Df_Localization_Model_Exporter
	 * @throws Exception
	 */
	public function writeTranslationToFile($moduleName, array $translation) {
		df_param_string($moduleName, 0);
		/** @var string[] $translation */
		$translation =
			$this->correctTranslationWithCsvFileData(
				$moduleName
				,$translation
			)
		;
		df_assert_array($translation);
		/** @var string $targetDir */
		$targetDir =
			df_concat_path(
				Mage::getBaseDir('var')
				,'translations'
				,df_mage()->core()->translateSingleton()->getLocale()
			)
		;
		if (!file_exists($targetDir)) {
			if (!mkdir($targetDir, null, true)) {
				throw new Exception('Cannot create $targetDir');
			}
		}

		/** @var string $targetFile */
		$targetFile = $targetDir . DS . $moduleName . '.csv';
		df_assert_string($targetFile);
		/** @var Varien_File_Csv $parser */
		$parser = new Varien_File_Csv();
		/** string[] @var $csvdata */
		$csvdata = array();
		foreach ($translation as $key => $value)
			/** @var string $value */
			/** @var string $value */
			$csvdata[]= array($key, $value)
		;
		$parser->saveData($targetFile, $csvdata);
		return $this;
	}

	/**
	 * @param string $module
	 * @param string[] $translation
	 * @return string[]
	 */
	private function correctTranslationWithCsvFileData($module, array $translation) {
		df_param_string($module, 0);
		/** @var string[] $result */
		$result =
			array_merge(
				$translation
				,$this->getTranslationFromCsv($module)
			)
		;
		return $result;
	}

	/** @return array */
	private function getTranslationByModules() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_map(
					array($this, 'addTranslationStringToModule')
					,array_map(
						array($this, 'convertSourceKeyToObject')
						,array_keys($this->getDb()->getItems())
					)
					,array_values($this->getDb()->getItems())
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Translation_Db */
	private function getDb() {return Df_Localization_Model_Translation_Db::s();}

	/**
	 * @param string $module
	 * @return string[]
	 */
	private function getTranslationFromCsv($module) {
		df_param_string($module, 0);
		/** @var string[] $result */
		$result = array();
		/** @var string $filePath */
		$filePath =
			df_concat_path(
				Mage::getBaseDir('locale')
				,df_mage()->core()->translateSingleton()->getLocale()
				,$module . '.csv'
			)
		;
		if (file_exists($filePath)) {
			/** @var Varien_File_Csv $parser */
			$parser = new Varien_File_Csv();
			$parser->setDelimiter(Mage_Core_Model_Translate::CSV_SEPARATOR);
			/** @var string[] $result */
			$result =
				$parser->getDataPairs(
					$filePath
				)
			;
			df_result_array($result);
		}
		return $result;
	}

	/**
	 * @param array $translation
	 * @return Df_Localization_Model_Exporter
	 */
	private function writeTranslationToFiles(array $translation) {
		array_map(
			array($this, 'writeTranslationToFile')
			,array_keys($translation)
			,array_values($translation)
		)
		;
		return $this;
	}
	/** @return Df_Localization_Model_Exporter */
	public static function i() {return new self;}
}