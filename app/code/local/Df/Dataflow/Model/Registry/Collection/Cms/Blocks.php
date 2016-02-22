<?php
class Df_Dataflow_Model_Registry_Collection_Cms_Blocks extends Df_Dataflow_Model_Registry_Collection {
	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Block_Collection
	 */
	protected function createCollection() {
		return Df_Cms_Model_Resource_Block_Collection::i($loadStoresInfo = true);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Cms_Model_Block::_CLASS;}

	/** @return Df_Dataflow_Model_Registry_Collection_Cms_Blocks */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}