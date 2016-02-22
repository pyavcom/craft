<?php
class Df_Seo_Model_Template_Processor extends Df_Core_Model_Abstract {
	/**
	 * @param array $params
	 * @return Df_Seo_Model_Template_Expression
	 */
	public function createExpression(array $params) {
		return
			Df_Seo_Model_Template_Expression::i(
				array(
					Df_Seo_Model_Template_Expression::P__PROCESSOR => $this
					,Df_Seo_Model_Template_Expression::P__RAW => df_a($params, 0)
					,Df_Seo_Model_Template_Expression::P__CLEAN => df_a($params, 1)
				)
			)
		;
	}

	/**
	 * Возвращает доступный в выражениях объект по имени.
	 * Выражении «product.manufacturer» имя объекта — «product», 
	 * и мы можем получить сам объект путём вызова
	 * $processor->getObject('product');
	 * @param string $name
	 * @return Varien_Object
	 */
	public function getObject($name) {return df_a($this->getObjects(), $name);}

	/** @return string */
	public function process() {return strtr($this->getText(), $this->getMappings());}

	/** @return array(string => Varien_Object) */
	protected function getObjects() {return $this->cfg(self::P__OBJECTS);}

	/** @return string */
	protected function getText() {return $this->cfg(self::P__TEXT);}

	/** @return array */
	private function getExpressions() {
		$result =
			array_map(
				array($this, 'createExpression')
				,preg_match_all(
					$this->getPattern()
					,$this->getText()
					,$matches
					,PREG_SET_ORDER
				)
				? $matches
				: array()
			)
		;
		return $result;
	}

	/** @return array */
	private function getMappings() {
		$result = array();
		foreach ($this->getExpressions() as $expression) {
			/** @var Df_Seo_Model_Template_Expression $expression */
			$result[$expression->getRaw()] = $expression->getResult();
		}
		return $result;
	}

	/** @return string */
	private function getPattern() {return '#{([^}]+)}#mui';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__TEXT, self::V_STRING)
			->_prop(self::P__OBJECTS, self::V_ARRAY)
		;
	}
	const _CLASS = __CLASS__;
	const P__OBJECTS = 'objects';
	const P__TEXT = 'text';
	/**
	 * @static
	 * @param string $text
	 * @param array(string => Varien_Object) $objects
	 * @return Df_Seo_Model_Template_Processor
	 */
	public static function i($text, array $objects) {return new self(array(
		self::P__TEXT => $text, self::P__OBJECTS => $objects
	));}
}