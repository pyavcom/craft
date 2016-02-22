<?php

$installer = $this;
$installer->startSetup();

$compatible = Mage::helper('opcheckout')->getCompatibility();
if ($compatible == 1) {
    $table = $this->getTable('sales_flat_order');
} else {
    $table = $this->getTable('sales_order');
}
$query = 'ALTER TABLE `' . $table . '` ADD COLUMN `order_comment` TEXT CHARACTER SET utf8 DEFAULT NULL';
$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
$connection->query($query);

$installer->endSetup();

