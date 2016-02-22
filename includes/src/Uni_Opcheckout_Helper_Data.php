<?php

require_once 'Mage/Checkout/Helper/Data.php';

class Uni_Opcheckout_Helper_Data extends Mage_Checkout_Helper_Data {

    const COMPATIBILITY_TYPE1 = 1; //for above
    const COMPATIBILITY_TYPE2 = 2; //for 1.4.0.0 and 1.4.0.1

    public function isOrderCommentEnabled() {
        Mage::helper('unicommon')->getExtensionUpdates();
        return Mage::getStoreConfigFlag('opcheckout/order/opcheckout_order_comment') ? true : false;
    }

    public function setOrderCommentCompatible1($observer) {
        if ($this->getCompatibility() != self::COMPATIBILITY_TYPE1) {
            return;
        }
        if ($this->isOrderCommentEnabled()) {
            $orderComment = trim($this->_getRequest()->getPost('order_comment'));
            if ($orderComment != "") {
                $observer->getEvent()->getOrder()->setOrderComment($orderComment);
            } else {
                $observer->getEvent()->getOrder()->setOrderComment(Mage::getSingleton('checkout/session')->getOpcheckoutOrderComment());
                Mage::getSingleton('checkout/session')->unsOpcheckoutOrderComment();
            }
        }
    }

    public function setOrderCommentCompatible2($observer) {
        if ($this->getCompatibility() != self::COMPATIBILITY_TYPE2) {
            return;
        }
        if ($this->isOrderCommentEnabled()) {
            $orderComment = trim($this->_getRequest()->getPost('order_comment'));
            if ($orderComment == "") {
                $orderComment = Mage::getSingleton('checkout/session')->getOpcheckoutOrderComment();
                Mage::getSingleton('checkout/session')->unsOpcheckoutOrderComment();
            }
            $order = $observer->getEvent()->getOrder();
            if ($order && ($orderComment != "")) {
                if ($order->getId()) {
                    $resource = Mage::getSingleton('core/resource');
                    $sales_order = $resource->getTableName('sales_order');
                    $resource->getConnection('core_write')->update($sales_order, array('order_comment' => $orderComment), array('entity_id = ?' => $order->getId()));
                }
            }
        }
    }

    public function isAllowedNewsletterSubscription() {
        $customerSession = Mage::getSingleton('customer/session');
        if (defined('Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG') && Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && !$customerSession->isLoggedIn()) {
            return false;
        } else {
            if ($customerSession->isLoggedIn()) {
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customerSession->getCustomer());
                return !$subscriber->isSubscribed() && Mage::getStoreConfig('opcheckout/order/opcheckout_newsletter_subscribe');
            }
            return Mage::getStoreConfig('opcheckout/order/opcheckout_newsletter_subscribe');
        }
    }

    public function isNewsletterSubscriptionChecked() {
        return (boolean) Mage::getStoreConfig('opcheckout/order/newsletter_checked_bydefault');
    }

    public function getCompatibility() {
        $version = Mage::getVersion();
        if (in_array($version, array('1.4.0.0', '1.4.0.1'))) {
            return self::COMPATIBILITY_TYPE2;
        } else {
            return self::COMPATIBILITY_TYPE1;
        }
    }
}