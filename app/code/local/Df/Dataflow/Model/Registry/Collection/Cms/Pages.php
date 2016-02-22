<?php
class Df_Dataflow_Model_Registry_Collection_Cms_Pages extends Df_Dataflow_Model_Registry_Collection {
	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Page_Collection
	 */
	protected function createCollection() {
		return Df_Cms_Model_Resource_Page_Collection::i($loadStoresInfo = true);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Cms_Model_Page::_CLASS;}

	/** @return Df_Dataflow_Model_Registry_Collection_Cms_Pages */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}


 