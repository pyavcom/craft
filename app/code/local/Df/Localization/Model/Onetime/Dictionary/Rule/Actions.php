<?php
class Df_Localization_Model_Onetime_Dictionary_Rule_Actions extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return Df_Localization_Model_Onetime_Dictionary_Terms */
	public function getTerms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Model_Onetime_Dictionary_Terms::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getTitleNew() {return $this->getEntityParam('new_title');}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param string $concreteClass
	 * @param Df_Varien_Simplexml_Element $simpleXml
	 * @return Df_Localization_Model_Onetime_Dictionary_Rule_Actions
	 */
	public static function createConcrete($concreteClass, Df_Varien_Simplexml_Element $simpleXml) {
		/** @var Df_Localization_Model_Onetime_Dictionary_Rule_Actions $result */
		$result = new $concreteClass(array(self::P__SIMPLE_XML => $simpleXml));
		df_assert($result instanceof Df_Localization_Model_Onetime_Dictionary_Rule_Actions);
		return $result;
	}
}