<?php
class Df_Review_Model_Resource_Review extends Mage_Review_Model_Mysql4_Review {
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Review_Model_Resource_Review */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}