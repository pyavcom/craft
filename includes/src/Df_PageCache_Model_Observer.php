<?php
class Df_PageCache_Model_Observer extends Df_Core_Model_Abstract {
	/**
	 * Save page body to cache storage
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function cacheResponse(Varien_Event_Observer $observer) {
		if ($this->isCacheEnabled()) {
			$frontController = $observer->getEvent()->getFront();
			$request = $frontController->getRequest();
			$response = $frontController->getResponse();
			$this->getProcessor()->processRequestResponse($request, $response);
		}
	}

	/**
	 * Check category state on post dispatch to allow category page be cached
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function checkCategoryState(Varien_Event_Observer $observer) {
		if ($this->isCacheEnabled()) {
			$category = Mage::registry('current_category');
			/**
			 * Categories with category event can't be cached
			 */
			if ($category && $category->getEvent()) {
				$request = $observer->getEvent()->getControllerAction()->getRequest();
				$request->setParam('no_cache', true);
			}
		}
	}

	/**
	 * Check product state on post dispatch to allow product page be cached
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function checkProductState(Varien_Event_Observer $observer) {
		if ($this->isCacheEnabled()) {
			$product = Mage::registry('current_product');
			/**
			 * Categories with category event can't be cached
			 */
			if ($product && $product->getEvent()) {
				$request = $observer->getEvent()->getControllerAction()->getRequest();
				$request->setParam('no_cache', true);
			}
		}
	}

	/** @return void */
	public function cleanCache() {df_h()->pageCache()->clean();}

	/**
	 * Invalidate full page cache
	 * @return Df_PageCache_Model_Observer
	 */
	public function invalidateCache() {
		Mage::app()->getCacheInstance()->invalidateType('full_page');
		return $this;
	}

	/**
	 * Check if full page cache is enabled
	 * @return bool
	 */
	public function isCacheEnabled() {
		/** @var bool $result */
		static $result;
		if (!isset($result)) {
			$result =
					Mage::app()->useCache('full_page')
				&&
					df_enabled(Df_Core_Feature::FULL_PAGE_CACHING)
			;
		}
		return $result;
	}

	/**
	 * Check when cache should be disabled
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function processPreDispatch(Varien_Event_Observer $observer) {
		if (!Df_PageCache_Model_Processor::isDisabledByCurrentUri() && $this->isCacheEnabled()) {
			$action = $observer->getEvent()->getControllerAction();
			/* @var $request Mage_Core_Controller_Request_Http */
			$request = $action->getRequest();
			/* @var $cookie Mage_Core_Model_Cookie */
			$cookie = df_mage()->core()->cookieSingleton();
			$cookieName = Df_PageCache_Model_Processor::NO_CACHE_COOKIE;
			$noCache = $cookie->get($cookieName);
			if ($noCache) {
				df_mage()->catalog()->sessionSingleton()->setParamsMemorizeDisabled(false);
				$cookie->renew($cookieName);
			}
			else if ($action) {
				df_mage()->catalog()->sessionSingleton()->setParamsMemorizeDisabled(true);
				if (
						$request->isPost()
					||
						in_array($action->getFullActionName(), $this->_cacheDisableActions)
				) {
					rm_session_core()->setNoCacheFlag(1);
					$cookie->set($cookieName, 1);
				}
			}
			/**
			 * Check if request will be cached
			 */
			if ($this->getProcessor()->canProcessRequest($request)) {
				// disable SID
				Mage::app()->setUseSessionInUrl(false);
				// disable blocks cache
				Mage::app()->getCacheInstance()->banUse(Mage_Core_Block_Abstract::CACHE_GROUP);
			}
			$this->_checkViewedProducts();
		}
		return $this;
	}

	/**
	 * model_load_after event processor.
	 * Collect tags of all loaded entities
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function registerModelTag(Varien_Event_Observer $observer) {
		if ($this->isCacheEnabled()) {
			/** @var Mage_Core_Model_Abstract $object */
			$object = $observer->getEvent()->getObject();
			if ($object && $object->getId()) {
				$tags = $object->getCacheIdTags();
				if ($tags) {
					$this->getProcessor()->addRequestTag($tags);
				}
			}
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function validateDataChanges(Varien_Event_Observer $observer) {
		if ($this->isCacheEnabled()) {
			Df_PageCache_Model_Validator::s()->checkDataChange($observer->getEvent()->getObject());
		}
	}

	/**
	 * Check if data delete affect cached pages
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function validateDataDelete(Varien_Event_Observer $observer) {
		if ($this->isCacheEnabled()) {
			Df_PageCache_Model_Validator::s()->checkDataChange($observer->getEvent()->getObject());
		}
	}

	/**
	 * Check if last viewed product id should be processed after cached product view
	 * @return void
	 */
	private function _checkViewedProducts() {
		$varName = Df_PageCache_Model_Processor::LAST_PRODUCT_COOKIE;
		/** @var Mage_Core_Model_Cookie $cookie */
		$cookie = df_mage()->core()->cookieSingleton();
		$productId = (int)$cookie->get($varName);
		if ($productId) {
			/** @var Mage_Reports_Model_Product_Index_Viewed $productIndexViewed */
			$productIndexViewed = df_model('reports/product_index_viewed');
			if (!$productIndexViewed->getCount()) {
				/** @var Df_Catalog_Model_Product $product */
				$product = df_product();
				$product->load($productId);
				if ($product->getId()) {
					$productIndexViewed->setProductId($productId);
					$productIndexViewed->save();
					$productIndexViewed->calculate();
				}
			}
			df_mage()->core()->cookieSingleton()->delete($varName);
		}
	}

	/** @return Df_PageCache_Model_Processor */
	private function getProcessor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_PageCache_Model_Processor::i();
		}
		return $this->{__METHOD__};
	}
	/** @var string[] */
	private $_cacheDisableActions = array('checkout_cart_add','catalog_product_compare_add');
}