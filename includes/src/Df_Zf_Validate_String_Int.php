<?php
class Df_Zf_Validate_String_Int extends Df_Zf_Validate_Type {
	/**
	 * @override
	 * @param string $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		/**
		 * Думаю, правильно будет конвертировать строки типа «09» в целые числа без сбоев.
		 * Обратите внимание, что
		 * 9 === intval('09').
		 *
		 * Обратите также внимание, что если строка равна '0',
		 * то нам применять @see ltrim нельзя, потому что иначе получим пустую строку.
		 */
		if ('0' !== $value) {
			$value = ltrim($value, '0');
		}
		return strval($value) === strval(intval($value));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'целое число';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'целого числа';}

	/** @return Df_Zf_Validate_String_Int */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}