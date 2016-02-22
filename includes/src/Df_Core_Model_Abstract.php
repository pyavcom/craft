<?php
abstract class Df_Core_Model_Abstract
	extends Mage_Core_Model_Abstract
	implements Df_Core_Destructable {
	/**
	 * Обратите внимание,
	 * что родительский деструктор вызывать не надо и по правилам PHP даже нельзя,
	 * потому что в родительском классе (и всех классах-предках)
	 * метод __destruct() не объявлен.
	 * @return void
	 */
	public function __destruct() {
		/**
		 * Для глобальных объекто-одиночек,
		 * чей метод @see Df_Core_Model_Abstract::isDestructableSingleton() возвращает true,
		 * метод @see Df_Core_Model_Abstract::_destruct()
		 * будет вызван на событие «controller_front_send_response_after»:
		 * @see Df_Core_Model_Dispatcher::controller_front_send_response_after().
		 */
		if (!$this->isDestructableSingleton()) {
			$this->_destruct();
		}
	}

	/**
	 * Размещайте программный код деинициализации объекта именно в этом методе,
	 * а не в стандартном деструкторе __destruct().
	 *
	 * Не все потомки класса @see Df_Core_Model_Abstract
	 * деинициализируется посредством стандартного деструктора.
	 *
	 * В частности, глобальные объекты-одиночки
	 * деинициализировать посредством глобального деструктора опасно,
	 * потому что к моменту вызова стандартного деструктора
	 * сборщик мусора Zend Engine мог уже уничтожить другие объекты,
	 * которые требуются для деинициализации.
	 *
	 * Метод @see Df_Core_Model_Abstract::_destruct() гарантированно
	 *
	 * Для глобальных объекто-одиночек,
	 * чей метод @see Df_Core_Model_Abstract::isDestructableSingleton() возвращает true,
	 * метод @see Df_Core_Model_Abstract::_destruct()
	 * будет вызван на событие «controller_front_send_response_after»:
	 * @see Df_Core_Model_Dispatcher::controller_front_send_response_after().
	 *
	 * @override
	 * @return void
	 */
	public function _destruct() {$this->cacheSave();}

	/** @return string */
	public function _getModuleName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a(explode('_', get_class($this)), 1);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $key
	 * @param mixed $default[optional]
	 * @return mixed
	 */
	public function cfg($key, $default = null) {
		/** @var mixed $result */
		/**
		 * Обратите внимание,
		 * что здесь нужно вызывать именно @see Df_Core_Model_Abstract::getData(),
		 * а не @see Varien_Object::_getData()
		 * чтобы работали валидаторы.
		 */
		$result = $this->getData($key);
		// Некоторые фильтры заменяют null на некоторое другое значение,
		// поэтому обязательно учитываем равенство null
		// значения свойства ДО применения фильтров.
		/** @var bool $valueWasNullBeforeFilters */
		$valueWasNullBeforeFilters = df_a($this->_valueWasNullBeforeFilters, $key, true);
		// Раньше вместо !is_null($result) стояло !$result.
		// !is_null выглядит логичней.
		return !is_null($result) && !$valueWasNullBeforeFilters ? $result : $default;
	}

	/** @return string */
	public function getCurrentClassNameInMagentoFormat() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_h()->core()->reflection()->getModelNameInMagentoFormat(get_class($this))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @throws Mage_Core_Exception
	 * @param string $key
	 * @param null|string|int $index
	 * @return mixed
	 */
	public function getData($key = '', $index = null) {
		/** @var mixed $result */
		if (('' === $key) || array_key_exists($key, $this->_data)) {
			$result = parent::getData($key, $index);
		}
		else {
			// Обрабатываем здесь только те случаи,
			// когда запрашиваются значения неицициализированных свойств объекта
			$result = $this->_applyFilters($key, null);
			// Обратите внимание, что фильтры и валидаторы применяются только единократно,
			// потому что повторно мы в эту ветку кода не попадём
			// из-за срабатывания условия array_key_exists($key, $this->_data) выше
			// (даже если фильтры для null вернут null, наличие ключа array('ключ' => null))
			// достаточно, чтобы не попадать в данную точку программы повторно.
			$this->_validate($key, $result);
			$this->_data[$key] = $result;
		}
		return $result;
	}

	/**
	 * @override
	 * @return string|int
	 */
	public function getId() {
		/** @var mixed $result */
		$result =
				(
						empty($this->_idFieldName)
					&&
						empty($this->_resourceName)
					&&
						is_null($this->_getData('id'))
				)
			?
				/**
				 * Объект родительского класса такую ситуации переводит в исключительную.
				 * Мы же, для использования модели в коллекциях, создаём идентификатор.
				 * Конечно, таким образом мы лишаемся возможности проверки объекта на новизну,
				 * однако, раз ресурсная модель не установлена,
				 * то такая проверка нам вроде бы и не нужна.
				 */
				$this->getAutoGeneratedId()
			:
				parent::getId()
		;
		return $result;
	}

	/**
	 * @override
	 * @param string|array(string => mixed) $key
	 * @param mixed $value
	 * @return Df_Core_Model_Abstract
	 */
	public function setData($key, $value = null) {
		/**
		 * Раньше мы проводили валидацию лишь при извлечении значения свойства,
		 * в методе @see Df_Core_Model_Abstract::getData().
		 * Однако затем мы сделали улучшение:
		 * перенести валидацию на более раннюю стадию — инициализацию свойства
		 * @see Df_Core_Model_Abstract::setData(),
		 * и инициализацию валидатора/фильтра
		 * @see Df_Core_Model_Abstract::_prop().
		 * Это улучшило диагностику случаев установки объекту некорректных значений свойств,
		 * потому что теперь мы возбуждаем исключительную ситуацию
		 * сразу при попытке установки некорректного значения.
		 * А раньше, когда мы проводили валидацию лишь при извлечении значения свойства,
		 * то при диагностике было не вполне понятно,
		 * когда конкретно объекту было присвоено некорректное значение свойства.
		 */
		if (is_array($key)) {
			$this->_checkForNullArray($key);
			$key = $this->_applyFiltersToArray($key);
			$this->_validateArray($key);
		}
		else {
			$this->_checkForNull($key, $value);
			$value = $this->_applyFilters($key, $value);
			$this->_validate($key, $value);
		}
		parent::setData($key, $value);
		return $this;
	}

	/**
	 * @override
	 * @param mixed $value
	 * @return Df_Core_Model_Abstract
	 */
	public function setId($value) {
		parent::setId($value ? $value : null);
		return $this;
	}

	/**
	 * @param string $key
	 * @param Zend_Validate_Interface|Df_Zf_Validate_Type|string|mixed[] $validator
	 * @param bool|null $isRequired [optional]
	 * @throws Df_Core_Exception_Internal
	 * @return Df_Core_Model_Abstract
	 */
	protected function _prop($key, $validator, $isRequired = null) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		/**
		 * Обратите внимание, что если метод _prop() был вызван с двумя параметрами,
		 * то и count($arguments) вернёт 2,
		 * хотя в методе _prop() всегда доступен и 3-х параметр: $isRequired.
		 * Другими словами, @see func_get_args() не возвращает параметры по умолчанию,
		 * если они не были реально указаны при вызове текущего метода.
		 */
		if (2 < count($arguments)) {
			$isRequired = df_array_last($arguments);
			/** @var bool $hasRequiredFlag */
			$hasRequiredFlag = is_bool($isRequired) || is_null($isRequired);
			if ($hasRequiredFlag) {
				$validator = array_slice($arguments, 1, -1);
			}
			else {
				$isRequired = null;
				$validator = df_array_tail($arguments);
			}
		}
		/** @var Zend_Validate_Interface[] $additionalValidators */
		$additionalValidators = array();
		/** @var Zend_Filter_Interface[] $additionalFilters */
		$additionalFilters = array();
		if (!is_array($validator)) {
			$validator =
				$this->_resolveValidatorOrFilter(
					$validator
					, $key
					/**
					 * Если значение флага $isRequired не указано,
					 * то ради ускорения системы используем в качестве валидатора объект-одиночку.
					 * Если же значение флага $isRequired указано,
					 * то использовать объект-одиночку нельзя:
					 * ведь для каждого такого валидатора значение флага может быть своим
					 */
					, $useSingleton = is_null($isRequired)
				)
			;
			df_assert($validator instanceof Zend_Validate_Interface);
		}
		else {
			/** @var array(Zend_Validate_Interface|Df_Zf_Validate_Type|string) $additionalValidatorsRaw */
			$additionalValidatorsRaw = df_array_tail($validator);
			$validator =
				$this->_resolveValidatorOrFilter(
					df_array_first($validator)
					, $key
					/**
					 * Если значение флага $isRequired не указано,
					 * то ради ускорения системы используем в качестве валидатора объект-одиночку.
					 * Если же значение флага $isRequired указано,
					 * то использовать объект-одиночку нельзя:
					 * ведь для каждого такого валидатора значение флага может быть своим
					 */
					, $useSingleton = is_null($isRequired)
				)
			;
			df_assert($validator instanceof Zend_Validate_Interface);
			foreach ($additionalValidatorsRaw as $additionalValidatorRaw) {
				/** @var Zend_Validate_Interface|Zend_Filter_Interface|string $additionalValidatorsRaw */
				/** @var Zend_Validate_Interface|Zend_Filter_Interface $additionalValidator */
				$additionalValidator = $this->_resolveValidatorOrFilter($additionalValidatorRaw, $key);
				if ($additionalValidator instanceof Zend_Validate_Interface) {
					$additionalValidators[]= $additionalValidator;
				}
				if ($additionalValidator instanceof Zend_Filter_Interface) {
					$additionalFilters[]= $additionalValidator;
				}
			}
		}
		$this->_addValidator($key, $validator, $isRequired);
		if ($validator instanceof Zend_Filter_Interface) {
			/** @var Zend_Filter_Interface $filter */
			$filter = $validator;
			$this->_addFilter($key, $filter);
		}
		foreach ($additionalFilters as $additionalFilter) {
			/** @var Zend_Filter_Interface $additionalFilter */
			$this->_addFilter($key, $additionalFilter);
		}
		/**
		 * Раньше мы проводили валидацию лишь при извлечении значения свойства,
		 * в методе @see Df_Core_Model_Abstract::getData().
		 * Однако затем мы сделали улучшение:
		 * перенести валидацию на более раннюю стадию — инициализацию свойства
		 * @see Df_Core_Model_Abstract::setData(),
		 * и инициализацию валидатора/фильтра
		 * @see Df_Core_Model_Abstract::_prop().
		 * Это улучшило диагностику случаев установки объекту некорректных значений свойств,
		 * потому что теперь мы возбуждаем исключительную ситуацию
		 * сразу при попытке установки некорректного значения.
		 * А раньше, когда мы проводили валидацию лишь при извлечении значения свойства,
		 * то при диагностике было не вполне понятно,
		 * когда конкретно объекту было присвоено некорректное значение свойства.
		 */
		/** @var bool $hasValueVorTheKey */
		$hasValueVorTheKey = array_key_exists($key, $this->_data);
		if ($hasValueVorTheKey) {
			$this->_validateByConcreteValidator($key, $this->_data[$key], $validator);
		}
		foreach ($additionalValidators as $additionalValidator) {
			/** @var Zend_Validate_Interface $additionalValidator */
			$this->_addValidator($key, $additionalValidator);
			if ($hasValueVorTheKey) {
				$this->_validateByConcreteValidator($key, $this->_data[$key], $additionalValidator);
			}
		}
		return $this;
	}
	/** @var array(string => Zend_Filter_Interface[]) */
	private $_filters = array();
	/** @var array(string => Zend_Validate_Interface[]) */
	private $_validators = array();

	/** @return void */
	protected function cacheSaveBefore() {}

	/** @return string[] */
	protected function getCacheKeyParamsAdditional() {return array();}

	/** @return int|null */
	protected function getCacheLifetime() {return null; /* пожизненно*/}

	/** @return string[] */
	protected function getCacheTagsRm() {return array();}

	/** @return string */
	protected function getCacheTypeRm() {return '';}

	/** @return string[] */
	protected function getPropertiesToCache() {return array();}

	/**
	 * Значения этих свойств кэшируются для каждого магазина отдельно
	 * @return string[]
	 */
	protected function getPropertiesToCachePerStore() {return array();}

	/**
	 * Если требующее кэширование свойство не является объектом или массимом, содержащим объекты,
	 * то перечислите это свойство в методе @see getPropertiesToCacheSimple(),
	 * и тогда свойство будет кэшироваться быстрее,
	 * потому что вместо функций @see serialize() / @see unserialize()
	 * будут применены более быстрые функции @see json_encode() / @see json_decode().
	 * вместо @see saveDataComplex() / @see loadDataComplex().
	 * @link http://stackoverflow.com/a/7723730/254475
	 * @link http://stackoverflow.com/a/804053/254475
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return array();}

	/**
	 * Если этот метод вернёт true,
	 * то система вызовет метод @see Df_Core_Model_Abstract::_destruct()
	 * не в стандартном деструкторе __destruct(),
	 * а на событие «controller_front_send_response_after»:
	 * @see Df_Core_Model_Dispatchercontroller_front_send_response_after().
	 *
	 * Опасно проводить деинициализацию глобальных объектов-одиночек
	 * в стандартном деструкторе __destruct(),
	 * потому что к моменту вызова деструктора для данного объекта-одиночки
	 * сборщик Zend Engine мог уже уничтожить другие глобальные объекты,
	 * требуемые при деинициализации (например, для сохранения кэша).
	 *
	 * @return bool
	 */
	protected function isDestructableSingleton() {return false;}

	/** @return bool */
	protected function isCacheEnabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					(
							!$this->getCacheTypeRm()
						||
							Mage::app()->getCacheInstance()->canUse($this->getCacheTypeRm())
					)
				&&
					(
							$this->getPropertiesToCache()
						||
							$this->getPropertiesToCachePerStore()
					)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Вызывайте этот метод для тех свойств,чьё кэшрованное значение изменилось.
	 * Такие свойства система заново сохранит в кэше в конце сеанса работы.
	 * Например, такое свойство может быть ассоциативным массивом,
	 * который заполняется постепенно, от сеанса к сеансу.
	 * Во время первого сеанса (начальное формирование кэша)
	 * могут быть заполнены лишь некоторые ключи такого массива
	 * (те, в которых была потребность в данном сеане),
	 * а вот во время следующих сеансов этот массив может дополняться новыми значениями.
	 * @param string $propertyName
	 * @return void
	 */
	protected function markCachedPropertyAsModified($propertyName) {
		$this->_cachedPropertiesModified[$propertyName] = true;
	}

	/**
	 * @param string $key
	 * @param Zend_Filter_Interface|string $filter
	 * @return void
	 */
	private function _addFilter($key, $filter) {
		if (is_string($filter)) {
			$filter = self::_getValidatorByName($filter);
		}
		df_assert(is_object($filter));
		df_assert($filter instanceof Zend_Filter_Interface);
		if (!isset($this->_filters[$key])) {
			$this->_filters[$key] = array();
		}
		$this->_filters[$key][] = $filter;
		/**
		 * Не используем @see isset(), потому что для массива
		 * $array = array('a' => null)
		 * isset($array['a']) вернёт false,
		 * что не позволит нам фильтровать значения параметров,
		 * сознательно установленные в null при конструировании объекта.
		 */
		if (array_key_exists($key, $this->_data)) {
			$this->_data[$key] = $filter->filter($this->_data[$key]);
		}
	}

	/**
	 * @param string $key
	 * @param Zend_Validate_Interface $validator
	 * @param bool|null $isRequired [optional]
	 * @return void
	 */
	private function _addValidator($key, Zend_Validate_Interface $validator, $isRequired = null) {
		/**
		 * Обратите внимание, что здесь ошибочно писать
		 		$isRequired = rm_bool($isRequired);
		 * потому что $isRequired может принимать не только значения true/false,
		 * но и отличное от них по смыслу значение null.
		 */
		/**
		 * Обратите внимание, что флаг $RM_VALIDATOR__REQUIRED надо устанавливать в любом случае,
		 * потому что у нас подавляющее большинство валидаторов является объектами-одиночками,
		 * и нам надо сбросить предыдущее значение $isRequired у этого объекта.
		 */
		$validator->{self::$RM_VALIDATOR__REQUIRED} = $isRequired;
		if (!isset($this->_validators[$key])) {
			$this->_validators[$key] = array();
		}
		$this->_validators[$key][] = $validator;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	private function _applyFilters($key, $value) {
		/** @var Zend_Filter_Interface[] $filters */
		$filters = df_a($this->_filters, $key, array());
		foreach ($filters as $filter) {
			/** @var Zend_Filter_Interface $filter */
			$value = $filter->filter($value);
		}
		return $value;
	}

	/**
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 */
	private function _applyFiltersToArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$params[$key] = $this->_applyFilters($key, $value);
		}
		return $params;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	private function _checkForNull($key, $value) {
		$this->_valueWasNullBeforeFilters[$key] = is_null($value);
	}
	/** @var array(string => bool) */
	private $_valueWasNullBeforeFilters = array();

	/**
	 * @param array(string => mixed) $params
	 * @return void
	 */
	private function _checkForNullArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$this->_checkForNull($key, $value);
		}
	}

	/**
	 * @param Zend_Validate_Interface|Zend_Filter_Interface|string $validator
	 * @param string $key
	 * @param bool $useSingleton [optional]
	 * @return Zend_Validate_Interface|Zend_Filter_Interface
	 */
	private function _resolveValidatorOrFilter($validator, $key, $useSingleton = true) {
		/** @var Zend_Validate_Interface|Zend_Filter_Interface $result */
		if (is_object($validator)) {
			$result = $validator;
		}
		else if (is_string($validator)) {
			$result = self::_getValidatorByName($validator, $useSingleton);
		}
		else {
			df_error_internal(
				'Валидатор/фильтр поля «%s» класса «%s» имеет недопустимый тип: «%s».'
				,$key, get_class($this), gettype($validator)
			);
		}
		if (
				!($result instanceof Zend_Validate_Interface)
			&&
				!($result instanceof Zend_Filter_Interface)
		) {
			df_error_internal(
				'Валидатор/фильтр поля «%s» класса «%s» имеет недопустимый класс «%s»,'
				. ' у которого отсутствуют требуемые интерфейсы'
				.' Zend_Validate_Interface и Zend_Filter_Interface.'
				,$key, get_class($this), get_class($result)
			);
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @throws Df_Core_Exception_Internal
	 * @return void
	 */
	private function _validate($key, $value) {
		/** @var @var array(Zend_Validate_Interface|Df_Zf_Validate_Type) $validators */
		$validators = df_a($this->_validators, $key, array());
		foreach ($validators as $validator) {
			/** @var Zend_Validate_Interface|Df_Zf_Validate_Type $validator */
			$this->_validateByConcreteValidator($key, $value, $validator);
		}
	}

	/**
	 * @param array(string => mixed) $params
	 * @throws Df_Core_Exception_Internal
	 * @return void
	 */
	private function _validateArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$params[$key] = $this->_validate($key, $value);
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param Zend_Validate_Interface|Df_Zf_Validate_Type $validator
	 * @throws Df_Core_Exception_Internal
	 * @return void
	 */
	private function _validateByConcreteValidator($key, $value, Zend_Validate_Interface $validator) {
		if (!(
				is_null($value)
			&&
				/**
				 * Обратите внимание, что если значение свойства равно NULL,
				 * то isset($validator->{self::$RM_VALIDATOR__REQUIRED}) вернёт false,
				 * в то время как @see property_exists() вернёт true.
				 */
				property_exists($validator, self::$RM_VALIDATOR__REQUIRED)
			&&
				/**
				 * Здесь должна стоять проверка именно на false,
				 * потому что помимо true/false
				 * $validator->{self::$RM_VALIDATOR__REQUIRED})
				 * может принимать значение null
				 */
				(false === $validator->{self::$RM_VALIDATOR__REQUIRED})
		)) {
			if (!$validator->isValid($value)) {
				/** @var string $compositeMessage */
				$compositeMessage =
					rm_sprintf(
						"«%s»: значение %s недопустимо для свойства «%s»."
						. "\r\nСообщение проверяющего:\r\n%s"
						,get_class($this)
						,df_h()->qa()->convertValueToDebugString($value)
						,$key
						,implode(Df_Core_Const::T_NEW_LINE, $validator->getMessages())
					)
				;
				$exception = new Df_Core_Exception_Internal($compositeMessage);
				/** @var Mage_Core_Model_Message $coreMessage */
				$coreMessage = df_model('core/message');
				$exception->addMessage(
					$coreMessage->error($compositeMessage, __CLASS__,  __METHOD__)
				);
				throw $exception;
			}
		}
	}

	/** @return Df_Core_Model_Abstract */
	private function cacheLoad() {
		if ($this->isCacheEnabled()) {
			$this->cacheLoadArea($this->getPropertiesToCache(), $this->getCacheKey());
			/**
			 * При вызове метода @see Df_Core_Model_Abstract::getCacheKeyPerStore()
			 * может произойти исключительная ситуация в том случае,
			 * когда текущий магазин системы ещё не инициализирован
			 * (вызов Mage::app()->getStore() приводит к исключительной ситуации),
			 * поэтому вызываем @see Df_Core_Model_Abstract::getCacheKeyPerStore()
			 * только если в этом методе есть реальная потребность,
			 * т.е. если класс действительно имеет свойства, подлежащие кэшированию в разрезе магазина,
			 * и текущий магазин уже инициализирован.
			 */
			if ($this->getPropertiesToCachePerStore() && Df_Core_Model_State::s()->isStoreInitialized()) {
				$this->cacheLoadArea($this->getPropertiesToCachePerStore(), $this->getCacheKeyPerStore());
			}
		}
		return $this;
	}

	/**
	 * @param string[] $propertyNames
	 * @param string $cacheKey
	 * @return void
	 */
	private function cacheLoadArea(array $propertyNames, $cacheKey) {
		if ($propertyNames) {
			$cacheKey = $cacheKey . '::';
			foreach ($propertyNames as $propertyName) {
				/** @var string $propertyName */
				$this->cacheLoadProperty($propertyName, $cacheKey);
			}
		}
	}

	/**
	 * @param string $propertyName
	 * @param string $cacheKey
	 * @return void
	 */
	private function cacheLoadProperty($propertyName, $cacheKey) {
		$cacheKey =  $cacheKey . $propertyName;
		/** @var string|bool $propertyValueSerialized */
		$propertyValueSerialized = Mage::app()->getCacheInstance()->load($cacheKey);
		if ($propertyValueSerialized) {
			/** @var mixed $propertyValue */
			/**
			 * Обратите внимание,
			 * что @see json_decode() в случае невозможности деколирования возвращает NULL,
			 * а @see unserialize в случае невозможности деколирования возвращает FALSE.
			 */
			$propertyValue =
				isset($this->_cachedPropertiesSimpleMap[$propertyName])
				? rm_unserialize_simple($propertyValueSerialized)
				: df_ftn(rm_unserialize($propertyValueSerialized))
			;
			if (!is_null($propertyValue)) {
				$this->_cachedPropertiesLoaded[$propertyName] = true;
				$this->$propertyName = $propertyValue;
			}
		}
	}

	/** @return Df_Core_Model_Abstract */
	private function cacheSave() {
		if ($this->isCacheEnabled()) {
			$this->cacheSaveBefore();
			$this->cacheSaveArea($this->getPropertiesToCache(), $this->getCacheKey());
			/**
			 * При вызове метода @see Df_Core_Model_Abstract::getCacheKeyPerStore()
			 * может произойти исключительная ситуация в том случае,
			 * когда текущий магазин системы ещё не инициализирован
			 * (вызов Mage::app()->getStore() приводит к исключительной ситуации),
			 * поэтому вызываем @see Df_Core_Model_Abstract::getCacheKeyPerStore()
			 * только если в этом методе есть реальная потребность,
			 * т.е. если класс действительно имеет свойства, подлежащие кэшированию в разрезе магазина,
			 * и если текущий магазин уже инициализирован.
			 */
			if ($this->getPropertiesToCachePerStore() && Df_Core_Model_State::s()->isStoreInitialized()) {
				$this->cacheSaveArea($this->getPropertiesToCachePerStore(), $this->getCacheKeyPerStore());
			}
		}
		return $this;
	}

	/**
	 * @buyer {buyer}
	 * @param string[] $propertyNames
	 * @param string $cacheKey
	 * @return void
	 */
	private function cacheSaveArea(array $propertyNames, $cacheKey) {
		if (!!$propertyNames) {
			$cacheKey = $cacheKey . '::';
			foreach ($propertyNames as $propertyName) {
				/** @var string $propertyName */
				if (
						isset($this->$propertyName)
					&&
						(
								/**
								 * Сохраняем в кэше только те свойства,
								 * которые либо еще не сохранены там,
								 * либо чьё значение изменилось после загрузки из кэша:
								 * @see Df_Core_Model_Abstract::markCachedPropertyAsModified()
								 */
								!isset($this->_cachedPropertiesLoaded[$propertyName])
							||
								isset($this->_cachedPropertiesModified[$propertyName])
						)

				) {
					$this->cacheSaveProperty($propertyName, $cacheKey);
				}
			}
		}
	}

	/**
	 * @param string $propertyName
	 * @param string $cacheKey
	 * @return void
	 */
	private function cacheSaveProperty($propertyName, $cacheKey) {
		$cacheKey = $cacheKey . $propertyName;
		/** @var string|bool $propertyValueSerialized */
		$propertyValueSerialized =
			isset($this->_cachedPropertiesSimpleMap[$propertyName])
			? rm_serialize_simple($this->$propertyName)
			: rm_serialize($this->$propertyName)
		;
		if ($propertyValueSerialized) {
			Mage::app()->getCacheInstance()->save(
				$data = $propertyValueSerialized
				,$id = $cacheKey
				,$tags = $this->getCacheTagsRm()
				,$lifeTime = $lifeTime = $this->getCacheLifetime()
			);
		}
	}

	/** @return string */
	private function getAutoGeneratedId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_uniqid();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getCacheKey() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode('::', array_merge(
				$this->getCacheKeyParams(), $this->getCacheKeyParamsAdditional()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getCacheKeyParams() {return array(get_class($this));}

	/** @return string */
	private function getCacheKeyPerStore() {
		if (!isset($this->{__METHOD__})) {
			if (!Df_Core_Model_State::s()->isStoreInitialized()) {
				df_error(
					'При кэшировании в разрезе магазина для объекта класса «%s» произошёл сбой,'
					. ' потому что система ещё не инициализировала текущий магазин.'
					, get_class($this)
				);
			}
			$this->{__METHOD__} = $this->getCacheKey() . '[' . Mage::app()->getStore()->getCode() . ']';
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_cachedPropertiesSimpleMap = array_flip($this->getPropertiesToCacheSimple());
		if ($this->_data) {
			$this->_checkForNullArray($this->_data);
			// Обратите внимание, что Mage::getModel
			// почему-то не устанавливает поле _hasModelChanged в true.
			$this->setDataChanges(true);
			/**
			 * Фильтры мы здесь пока применять не можем,
			 * потому что они ещё не инициализированны
			 * (фильтры будут инициализированы потомками
			 * уже после вызова @see Df_Core_Model_Abstract::_construct()).
			 * Вместо этого применяем фильтры для начальных данных
			 * в методе @see Df_Core_Model_Abstract::_prop(),
			 * а для дополнительных данных — в методе @see Df_Core_Model_Abstract::setData().
			 */
		}
		parent::_construct();
		$this->cacheLoad();
		if ($this->isDestructableSingleton()) {
			rm_destructable_singleton($this);
		}
	}

	/** @var string  */
	protected $_eventObject = 'object';
	/** @var string  */
	protected $_eventPrefix = 'df_core_abstract';
	/** @var array(string => bool) */
	private $_cachedPropertiesLoaded = array();
	/** @var array(string => bool) */
	private $_cachedPropertiesModified = array();
	/** @var array(string => null) */
	private $_cachedPropertiesSimpleMap;

	/** @var string */
	private static $RM_VALIDATOR__REQUIRED = 'rm__required';
	const _CLASS = __CLASS__;
	const ID_SUFFIX = '_id';
	const F_TRIM = 'filter-trim';
	const V_ARRAY = 'array';
	const V_BOOL = 'boolean';
	const V_FLOAT = 'float';
	const V_INT = 'int';
	const V_NAT = 'nat';
	const V_NAT0 = 'nat0';
	const V_STRING_NE = 'string';
	const V_STRING = 'string_empty';

	/**
	 * @param string $class
	 * @param string|string[] $functions
	 * @return string[]
	 */
	protected static function m($class, $functions) {
		df_assert($functions);
		/** @var string[] $result */
		$result = array();
		if (!is_array($functions)) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$functions = df_array_tail($arguments);
		}
		foreach ($functions as $function) {
			/** @var string $function */
			$result[]= $class . '::' . $function;
		}
		return $result;
	}

	/**
	 * @static
	 * @param string $validatorName
	 * @param bool $useSingleton [optional]
	 * @return Zend_Validate_Interface
	 */
	private static function _getValidatorByName($validatorName, $useSingleton = true) {
		/** @var array(string => Zend_Validate_Interface) $map */
		static $map;
		if (!isset($map)) {
			$map = array(
				self::F_TRIM => Df_Zf_Filter_String_Trim::s()
				,self::V_ARRAY => Df_Zf_Validate_Array::s()
				,self::V_BOOL => Df_Zf_Validate_Boolean::s()
				,self::V_FLOAT => Df_Zf_Validate_Float::s()
				,self::V_INT => Df_Zf_Validate_Int::s()
				,self::V_NAT => Df_Zf_Validate_Nat::s()
				,self::V_NAT0 => Df_Zf_Validate_Nat0::s()
				,self::V_STRING => Df_Zf_Validate_String::s()
				,self::V_STRING_NE => Df_Zf_Validate_String_NotEmpty::s()
			);
		}
		/** @var Zend_Validate_Interface $result */
		$result = df_a($map, $validatorName);
		if ($result) {
			if (!$useSingleton) {
				/** @var string $class */
				$class = get_class($result);
				$result = new $class;
			}
		}
		else {
			if (@class_exists($validatorName) || @interface_exists($validatorName)) {
				$result =
					$useSingleton
					? Df_Zf_Validate_Class::s($validatorName)
					: Df_Zf_Validate_Class::i($validatorName)
				;
			}
			else {
				df_error_internal('Система не смогла распознать валидатор «%s».', $validatorName);
			}
		}
		return $result;
	}
}