<?php
class Df_Localization_Model_Onetime_Dictionary_Term extends Df_Core_Model_SimpleXml_Parser_Entity {
	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getFrom();}

	/** @return string|null */
	public function getFrom() {return $this->getEntityParam('from');}

	/** @return string */
	public function getFromNormalized() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_normalize($this->getFrom());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getTo() {return $this->getEntityParam('to');}

	/** @return bool */
	public function isItRegEx() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_text()->isRegex($this->getFrom());
		}
		return $this->{__METHOD__};
	}

	/** Используется из @see Df_Localization_Model_Onetime_Dictionary_Terms::getItemClass() */
	const _CLASS = __CLASS__;
}