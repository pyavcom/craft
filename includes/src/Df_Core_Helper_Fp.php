<?php
class Df_Core_Helper_Fp extends Mage_Core_Helper_Abstract {
	/**
	 * @param string[]|string $function
	 * @param array|Iterator $array
	 * @param mixed $params
	 * @return array|ArrayIterator
	 */
	public function map($function, $array, $params = array()) {
		if (!is_array($params)) {
			$params = array($params);
		}
		return
			($array instanceof Iterator)
			? $this->mapIterator($function, $array, $params)
			: $this->mapArray($function, $array, $params)
		;
	}

	/**
	 * @param array|string $function
	 * @param Iterator $iterator
	 * @param array $params
	 * @return ArrayIterator
	 */
	private function mapIterator($function, Iterator $iterator, $params = array()) {
		return new ArrayIterator($this->mapArray($function, iterator_to_array($iterator), $params));
	}

	/**
	 * @param string[]|string $function
	 * @param array $array
	 * @param mixed $params
	 * @return array
	 */
	private function mapArray($function, array $array, $params = array()) {
		$result = array();
		foreach ($array as $item) {
			$result[]= call_user_func_array($function, array_merge(array($item), $params));
		}
		return $result;
	}

	/** @return Df_Core_Helper_Fp */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}