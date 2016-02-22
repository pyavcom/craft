<?php
class Df_Admin_Model_Acl extends Mage_Admin_Model_Acl {
	/**
	 * @override
	 * @param Zend_Acl_Resource_Interface $resource
	 * @param Zend_Acl_Resource_Interface|string $parent
	 * @throws Zend_Acl_Exception
	 * @return Df_Admin_Model_Acl
	 */
	public function add(Zend_Acl_Resource_Interface $resource, $parent = null) {
		if (!$this->has($resource)) {
			parent::add($resource);
		}
		return $this;
	}
}