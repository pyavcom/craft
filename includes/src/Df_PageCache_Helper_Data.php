<?php
class Df_PageCache_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return void */
	public function clean() {Mage::app()->cleanCache(Df_PageCache_Model_Processor::CACHE_TAG);}
	/** @return Df_PageCache_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}