<?php
class Df_Core_Model_Translate extends Mage_Core_Model_Translate {
	/**
	 * Retrive translated template file
	 * @override
	 * @param string $file
	 * @param string $type
	 * @param string|null $localeCode[optional]
	 * @return string|bool
	 */
	public function getTemplateFile($file, $type, $localeCode = null) {
		if (
				is_null($localeCode)
			||
				// В ядре Magento стоит обратное условие: видимо, там дефект
				(0 === preg_match('/[^a-zA-Z_]/', $localeCode))
		) {
			$localeCode = $this->getLocale();
		}
		// НАЧАЛО ЗАПЛАТКИ
		if (
				(Df_Core_Const::LOCALE__RUSSIAN === $localeCode)
			&&
				(
						(
								df_enabled(Df_Core_Feature::LOCALIZATION)
							&&
								$this->getConfigTranslation()->email()->isEnabled()
						)
					||
						rm_contains($file, 'df' . DS)
				)
		) {
			$localeCode = self::LOCALE__RU_DF;
		}
		// КОНЕЦ ЗАПЛАТКИ
		/** @var string $filePath */
		$filePath = 
			$this->getTemplateFilePathForLocale(
				$localeCode
				,$fileType = $type
				,$fileName = $file
			)
		;
		if (!file_exists($filePath)) {
			// If no template specified for this locale, use store default
			$filePath =
				$this->getTemplateFilePathForLocale(
					$localeCode = Mage::app()->getLocale()->getDefaultLocale()
					,$fileType = $type
					,$fileName = $file
				)				
			;
		}
		if (!file_exists($filePath)) {
			// If no template specified as store default locale, use en_US
			$filePath =
				$this->getTemplateFilePathForLocale(
					$localeCode = Mage_Core_Model_Locale::DEFAULT_LOCALE
					,$fileType = $type
					,$fileName = $file
				)					
			;
		}
		/** @var Varien_Io_File $ioAdapter */
		$ioAdapter = new Varien_Io_File();
		$ioAdapter->open(array('path' => $this->getBaseDirLocale()));
		/** @var string $result */
		$result = (string)$ioAdapter->read($filePath);
		return $result;
	}

	/**
	 * @override
	 * @param string $area
	 * @param bool $forceReload [optional]
	 * @return Df_Core_Model_Translate
	 */
	public function init($area, $forceReload = false) {
		if (!$this->needDisableTranslation()) {
			parent::init($area, $forceReload);
		}
		else {
			$this->_translateInline = false;
			$this->_data = array();
		}
		return $this;
	}

	/**
	 * @param string $text
	 * @param string $moduleName
	 * @return string
	 */
	public function translateFast($text, $moduleName) {
		if (!isset($this->{__METHOD__}[$moduleName][$text])) {
			/** @var string $code */
			$code = $moduleName . self::SCOPE_SEPARATOR . $text;
			/** @var string $result */
			$result = $this->_getTranslatedString($text, $code);
			$this->{__METHOD__}[$moduleName][$text] = $result;
		}
		return $this->{__METHOD__}[$moduleName][$text];
	}

	/**
	 * @override
	 * @param array(string => string) $data
	 * @param string|bool|int $scope
	 * @param bool $forceReload[optional]
	 * @return Mage_Core_Model_Translate
	 *
	 * В качестве $scope метод может получать:
	 * 1) имя модуля (для перевода модулей)
	 * 2) значение false (для перевода офоррмительских тем),
	 * @see Mage_Core_Model_Translate::_loadThemeTranslation()
	 * 3) целочисленный идентификатор магазина (для перевода из БД),
	 * @see Mage_Core_Model_Translate::_loadDbTranslation()
	 */
	protected function _addData($data, $scope, $forceReload = false) {
		/** @var bool $allowInterference */
		static $allowInterference;
		if (!isset($allowInterference)) {
			/** @var string $allowInterferenceAsString */
			$allowInterferenceAsString =
				df_is_admin()
				? $this->getConfigTranslation()->admin()->allowInterference()
				: $this->getConfigTranslation()->frontend()->allowInterference()
			;
			if (is_null($allowInterferenceAsString)) {
				/**
				 * Как ни странно, в магазине shop.d-m-t.ru
				 * метод allowInterference возвращает NULL.
				 * @link http://magento-forum.ru/topic/3703/
				 */
				$allowInterferenceAsString =
					Df_Admin_Model_Config_Source_YesNoDev::VALUE__DEVELOPER_MODE
				;
			}
			df_assert_string($allowInterferenceAsString);
			$allowInterference =
					!Mage::getIsDeveloperMode()
				?
					(
							Df_Admin_Model_Config_Source_YesNoDev::VALUE__NO
						!==
							$allowInterferenceAsString
					)
				:
					(
							Df_Admin_Model_Config_Source_YesNoDev::VALUE__YES
						===
							$allowInterferenceAsString
					)
			;
		}
		foreach ($data as $key => $value) {
			if ($key === $value) {
				continue;
			}
			$key = $this->_prepareDataString($key);
			$value = $this->_prepareDataString($value);
			/** @var string|bool|int|null $currentScope */
			$currentScope = is_null($this->_dataScope) ? null : df_a($this->_dataScope, $key);
			if ($scope && $currentScope && !$forceReload) {
				// В словаре уже имеется перевод для фразы $key.
				// Этот перевод принадлежит модулю $currentScope.
				/** @var string $keyWithCurrentSkope */
				$keyWithCurrentSkope = $currentScope . self::SCOPE_SEPARATOR . $key;
				if (!isset($this->_data[$keyWithCurrentSkope])) {
					// Перевод фразы $key хоть и принадлежит модулю $currentScope,
					// но (почему-то) является глобальным.
					if (isset($this->_data[$key])) {
						// Делаем перевод фразы $key из глобального локальным
						// (указываем его принадлежность модулю $currentScope посредством приставки)
						$this->_data[$keyWithCurrentSkope] = $this->_data[$key];
						// При необходимости (в соответствии с выбранными администратором настройками)
						// запрещаем модулям использовать переводы других модулей.
						// НАЧАЛО ЗАПЛАТКИ
						if (!$allowInterference) {
							unset($this->_data[$key]);
						}
						// КОНЕЦ ЗАПЛАТКИ
					}
				}
				/** @var string $keyWithNewSkope */
				$keyWithNewSkope = $scope . self::SCOPE_SEPARATOR . $key;
				// Добавляем новый, локальный для модуля $scope, перевод фразы $key
				$this->_data[$keyWithNewSkope] = $value;
			}
			else {
				// Сюда мы попадаем в трёх ситуациях:
				// 1) когда явно указан флаг $forceReload
				// 2) когда фразу $key ещё никто не переводил
				// 3) когда при вызове данного метода не указан владелец перевода ($scope)
				$this->_data[$key] = $value;
				$this->_dataScope[$key] = $scope;
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @param string $text
	 * @param string $code
	 * @return string
	 */
	protected function _getTranslatedString($text, $code) {
		/** @var string $result */
		$result = null;
		/** @var bool $needUseRmTranslator */
		static $needUseRmTranslator;
		if (!isset($needUseRmTranslator)) {
			$needUseRmTranslator =
				/**
				 * Не используем Df_Localization_Model_Realtime_Translator
				 * в процессе установки Magento Community Edition,
				 * потому что в это время Российская сборка ещё не установлена и не инициализирована,
				 * и использование Df_Localization_Model_Realtime_Translator::s() приводит к сбою
				 * Call to undefined function df_model()
				 * (и, видимо, к дальшейшим сбоям, даже если мы будем использовать df_model
				 * вместо df_model)
				 */
				Mage::isInstalled()
			&&
				/**
				 * Не используем Df_Localization_Model_Realtime_Translator
				 * в процессе обновления сторонних модулей,
				 * потому что в это время Российская сборка ещё не установлена и не инициализирована,
				 * и использование Df_Localization_Model_Realtime_Translator::s() приводит к сбою
				 * Call to undefined function df_model()
				 * (и, видимо, к дальшейшим сбоям, даже если мы будем использовать df_model
				 * вместо df_model)
				 */
				function_exists('df_model')
			&&
				Df_Localization_Model_Realtime_Translator::s()->isEnabled()
			;
		}
		if ($needUseRmTranslator) {
			/** @var Df_Localization_Model_Realtime_Translator */
			static $rmTranslator;
			if (!isset($rmTranslator)) {
				$rmTranslator = Df_Localization_Model_Realtime_Translator::s();
			}
			$result = $rmTranslator->translate($text, $code);
		}
		if (is_null($result) || ($text === $result)) {
			/**
			 * Раньше тут стояло:
			 * $result = parent::_getTranslatedString($text, $code);
			 * Вместо вызова родительского метода
			 * @see Mage_Core_Model_Translate::_getTranslatedString()
			 * реализуем его алгоритм несколько другим, более быстрым способом:
			 * заменив @see array_key_exists на @see isset
			 * @link http://stackoverflow.com/a/700257/254475
			 */
			if (!$this->_data) {
				$result = $text;
			}
			else if (isset($this->_data[$code])) {
				$result = $this->_data[$code];
			}
			else if (isset($this->_data[$text])) {
				$result = $this->_data[$text];
			}
			else {
				$result = $text;
			}
		}
		if (false && Df_Localization_Model_Realtime_Translator::$needLog) {
			Df_Localization_Model_Realtime_Translator::s()->log('text: ' . $text);
			Df_Localization_Model_Realtime_Translator::s()->log('code: ' . $code);
			Df_Localization_Model_Realtime_Translator::s()->log('result: ' . $result);
			/** @var bool $isFirstTime */
			static $isFirstTime = true;
			if ($isFirstTime && $this->getData()) {
				Mage::log(
					$this->getData()
					, $level = null
					, $file = 'rm.translation.dictionary.log'
					, $forceLog = true
				);
				$isFirstTime = false;
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @param string $moduleName
	 * @param array $files
	 * @param bool $forceReload
	 * @return Df_Core_Model_Translate
	 */
	protected function _loadModuleTranslation($moduleName, $files, $forceReload=false) {
		/** @var bool $localeIsRussian */
		static $localeIsRussian;
		if (!isset($localeIsRussian)) {
			$localeIsRussian = df_h()->localization()->locale()->isRussian();
		}
		/** @var bool $needEnableRmTranslation */
		$needEnableRmTranslation = $this->needEnableRmTranslation();
		/** @var bool $needSetRmTranslationAsPrimary */
		$needSetRmTranslationAsPrimary = $this->needSetRmTranslationAsPrimary();
		/** @var string $ruDfBasePath */
		static $ruDfBasePath;
		if (!isset($ruDfBasePath)) {
			$ruDfBasePath =
				df_concat_path(
					$this->getBaseDirLocale()
					,self::LOCALE__RU_DF
					,''
				)
			;
		}
		foreach ($files as $file) {
			/** @var string $file */
			/** @var string[] $paths */
			$paths = array();
			/** @var bool $isItRmModule */
			/**
			 * Раньше тут стоял код:
			 * $isItRmModule = rm_starts_with($file, 'Df_');
			 * Измнил этот код ради ускорения.
			 */
			$isItRmModule = ('Df_' === substr($file, 0, 3));
			if ($localeIsRussian && ($isItRmModule || $needEnableRmTranslation)) {
				$paths[]=
					/**
					 * Работает в 2 раза быстрее, чем implode
					 * @link http://stackoverflow.com/questions/4502654/php-many-concats-or-one-implode
					 */
					$ruDfBasePath . $file
				;
			}
			if (!$isItRmModule) {
				$paths[]= $this->_getModuleFilePath($moduleName, $file);
			}
			if ($needSetRmTranslationAsPrimary) {
				$paths = array_reverse($paths);
			}
			foreach ($paths as $path) {
				$this->_addData($this->_getFileData($path), $moduleName, $forceReload);
			}
		}
		return $this;
	}

	/** @return string */
	private function getBaseDirLocale() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getBaseDir('locale');
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Settings_Translation */
	private function getConfigTranslation() {return Df_Localization_Model_Settings_Translation::s();}

	/**
	 * @param string $localeCode
	 * @param string $fileType
	 * @param string $fileName
	 * @return string
	 */
	private function getTemplateFilePathForLocale($localeCode, $fileType, $fileName) {
		df_param_string($localeCode, 0);
		return
			df_concat_path(
				$this->getBaseDirLocale(), $localeCode, 'template', $fileType, $fileName
			)
		;
	}

	/** @return bool */
	private function isRuRuExist() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = is_dir($this->getBaseDirLocale() . DS . 'ru_RU');
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needDisableTranslation() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result = false;
			/** @var array(string => string)|string $suffixesRaw */
			$suffixes = Mage::getConfig()->getNode('df/disable-translation')->asArray();
			if (is_array($suffixes)) {
				/** @var string $uri */
				$uri = Mage::app()->getRequest()->getRequestUri();
				foreach ($suffixes as $suffix) {
					/** @var string $suffix */
					if (rm_contains($uri, $suffix)) {
						$result = true;
						break;
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needEnableRmTranslation() {
		/** @var bool $result */
		static $result;
		if (!isset($result)) {
			/** @var bool $result */
			$result =
					df_h()->localization()->locale()->isRussian()
				&&
					(
							// Используем для экранов установки перевод Российской сборки Magento
							!Mage::isInstalled()
						||
							// В системе выбрана русская локаль и присутствует только перевод
							// Российской сборки Magento, поэтому автоматически включаем его
							// и назначаем основным
							!$this->isRuRuExist()
						||
							(
								df_is_admin()
								? $this->getConfigTranslation()->admin()->isEnabled()
								: $this->getConfigTranslation()->frontend()->isEnabled()
							)
					)
			;
		}
		return $result;
	}

	/** @return bool */
	private function needSetRmTranslationAsPrimary() {
		/** @var bool $result */
		static $result;
		if (!isset($result)) {
			/** @var bool $result */
			$result =
					// Используем для экранов установки перевод Российской сборки Magento
					!Mage::isInstalled()
				||
					// В системе выбрана русская локаль и присутствует только перевод
					// Российской сборки Magento, поэтому автоматически включаем его
					// и назначаем основным
					!$this->isRuRuExist()
				||
					(
						df_is_admin()
						? $this->getConfigTranslation()->admin()->needSetAsPrimary()
						: $this->getConfigTranslation()->frontend()->needSetAsPrimary()
					)
			;
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	const LOCALE__RU_DF = 'ru_DF';
	/** @return Df_Core_Model_Translate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}