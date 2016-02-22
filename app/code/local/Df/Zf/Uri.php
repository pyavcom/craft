<?php
class Df_Zf_Uri extends Df_Core_Model_Abstract {
	/**
	 * @param Zend_Uri_Http $uri1
	 * @param Zend_Uri_Http $uri2
	 * @return bool
	 */
	public function areEqual(Zend_Uri_Http $uri1, Zend_Uri_Http $uri2) {
		return df_strings_are_equal_ci($this->toStringRm($uri1), $this->toStringRm($uri2));
	}

	/**
	 * @param Zend_Uri_Http $uri
	 * @return Zend_Uri_Http
	 */
	private function adjustPort(Zend_Uri_Http $uri) {
		/** @var Zend_Uri_Http $result */
		if ($uri->getPort() && ('80' === strval($uri->getPort()))) {
			$result = clone $uri;
			$result->setPort('');
		}
		else {
			$result = $uri;
		}
		return $result;
	}

	/**
	 * @param Zend_Uri_Http $uri
	 * @return string
	 */
	private function toStringRm(Zend_Uri_Http $uri) {return $this->adjustPort($uri)->__toString();}

	/** @return Df_Zf_Uri */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}