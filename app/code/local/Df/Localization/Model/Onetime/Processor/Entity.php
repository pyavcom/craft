<?php
abstract class Df_Localization_Model_Onetime_Processor_Entity extends Df_Core_Model_Abstract {
	/** @return string[] */
	abstract protected function getTitlePropertyName();

	/** @return void */
	public function process() {
		/**
		 * Обратите внимание, что описанный в словаре объект
		 * запросто может отсутствовать в Баже данных интернет-магазина
		 * (например, если он был удалён администратором).
		 */
		if ($this->getEntity()) {
			$this->updateTitle();
			foreach ($this->getActions()->getTerms() as $term) {
				/** @var Df_Localization_Model_Onetime_Dictionary_Term $term */
				$this->processTerm($term);
			}
		}
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Rule_Actions */
	protected function getActions() {return $this->cfg(self::$P__ACTIONS);}

	/** @return Mage_Core_Model_Abstract */
	protected function getEntity() {return $this->cfg(self::$P__ENTITY);}

	/** @return string[] */
	protected function getTranslatableProperties() {return array();}

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return void
	 */
	protected function processTerm(Df_Localization_Model_Onetime_Dictionary_Term $term) {
		foreach ($this->getTranslatableProperties() as $property) {
			/** @var string $property */
			/** @var string|null $textProcessed */
			$textProcessed = $this->translate($this->getEntity()->getData($property), $term);
			if (!is_null($textProcessed)) {
				$this->getEntity()->setData($property, $textProcessed);
			}
		}
	}

	/**
	 * @param string $newTitle
	 * @return void
	 */
	protected function setTitle($newTitle) {
		$this->getEntity()->setData($this->getTitlePropertyName(), $newTitle);
	}

	/**
	 * @param string|null|mixed $textOriginal
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return string|null
	 */
	protected function translate($textOriginal, Df_Localization_Model_Onetime_Dictionary_Term $term) {
		/** @var string|null $result */
		$result = null;
		if ($textOriginal && is_string($textOriginal)) {
			/** @var string $textProcessed */
			if ($term->isItRegEx()) {
				$textProcessed = preg_replace($term->getFrom(), $term->getTo(), $textOriginal);
				/**
				 * Вызываем setData() только при реальном изменении значения свойства,
				 * чтобы не менять попусту значение hasDataChanges
				 * (что потом приведёт к ненужным сохранениям объектов).
				 */
				if ($textProcessed !== $textOriginal) {
					$result = $textProcessed;
				}
			}
			else {
				/** @var string $textOriginalNormalized */
				$textOriginalNormalized = rm_normalize($textOriginal);
				$textProcessed =
					str_replace($term->getFromNormalized(), $term->getTo(), $textOriginalNormalized)
				;
				/**
				 * Вызываем setData() только при реальном изменении значения свойства,
				 * чтобы не менять попусту значение hasDataChanges
				 * (что потом приведёт к ненужным сохранениям объектов).
				 */
				if ($textProcessed !== $textOriginalNormalized) {
					$result = $textProcessed;
				}
			}
		}
		return $result;
	}

	/** @return void */
	protected function updateTitle() {
		if ($this->getActions()->getTitleNew()) {
			$this->setTitle($this->getActions()->getTitleNew());
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ACTIONS, Df_Localization_Model_Onetime_Dictionary_Rule_Actions::_CLASS);
		$this->_prop(self::$P__ENTITY, 'Mage_Core_Model_Abstract');
	}
	/** @var string */
	protected static $P__ENTITY = 'entity';
	/** @var string */
	private static $P__ACTIONS = 'actions';

	/**
	 * @param string $className
	 * @param Mage_Core_Model_Abstract $entity
	 * @param Df_Localization_Model_Onetime_Dictionary_Rule_Actions $actions
	 * @return void
	 */
	public static function processStatic(
		$className
		, Mage_Core_Model_Abstract $entity
		, Df_Localization_Model_Onetime_Dictionary_Rule_Actions $actions
	) {
		/** @var Df_Localization_Model_Onetime_Processor_Entity $processor */
		$processor = new $className(array(self::$P__ENTITY => $entity, self::$P__ACTIONS => $actions));
		df_assert($processor instanceof Df_Localization_Model_Onetime_Processor_Entity);
		$processor->process();
	}
}