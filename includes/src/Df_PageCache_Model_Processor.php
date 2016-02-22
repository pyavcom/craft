<?php
class Df_PageCache_Model_Processor extends Df_Core_Model_Abstract {
	/**
	 * Associate tag with page cache request identifier
	 * @param array|string $tag
	 * @return Df_PageCache_Model_Processor
	 */
	public function addRequestTag($tag) {
		if (is_array($tag)) {
			$this->_requestTags = array_merge($this->_requestTags, $tag);
		} else {
			$this->_requestTags[]= $tag;
		}
		return $this;
	}

	/**
	 * Do basic validation for request to be cached
	 * @param Zend_Controller_Request_Http $request
	 * @return bool
	 */
	public function canProcessRequest(Zend_Controller_Request_Http $request) {
		$result = $this->isAllowed();
		$result = $result && Mage::app()->useCache('full_page');
		if ($request->getParam('no_cache')) {
			$result = false;
		}
		if ($result) {
			$maxDepth = Mage::getStoreConfig(self::XML_PATH_ALLOWED_DEPTH);
			$queryParams = $request->getQuery();
			$result = count($queryParams)<=$maxDepth;
		}
		if ($result) {
			$multicurrency = Mage::getStoreConfig(self::XML_PATH_CACHE_MULTICURRENCY);
			if (!$multicurrency && !empty($_COOKIE['currency'])) {
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function extractContent($content) {
		if (!$content && $this->isAllowed()) {
			$content = Mage::app()->loadCache($this->getRequestCacheId());
		}
		return $content;
	}

	/** @return string */
	public function getRequestCacheId() {return $this->_requestCacheId;}
	
	/** @return string */
	public function getRequestId() {return $this->_requestId;}
	
	/**
	 * @param string $id
	 * @return string
	 */
	public function prepareCacheId($id) {return self::REQUEST_ID_PREFIX.md5($id);}

	/** @return bool */
	public function isAllowed() {return !$this->isDisabled();}

	/**
	 * Get cache request associated tags
	 * @return string[]
	 */
	public function getRequestTags() {return $this->_requestTags;}

	/**
	 * Process response body by specific request
	 * @param Zend_Controller_Request_Http $request
	 * @param Zend_Controller_Response_Http $response
	 * @return Df_PageCache_Model_Processor
	 */
	public function processRequestResponse(
		Zend_Controller_Request_Http $request
		, Zend_Controller_Response_Http $response
	) {
		if ($this->canProcessRequest($request)) {
			$processor = $this->getRequestProcessor($request);
			if ($processor && $processor->allowCache($request)) {
				$cacheId = $this->prepareCacheId($processor->getRequestUri($this, $request));
				$content = $processor->prepareContent($response);
				$lifetime = Mage::getStoreConfig(self::XML_PATH_LIFE_TIME)*60;
				Mage::app()->saveCache($content, $cacheId, $this->getRequestTags(), $lifetime);
//				Mage::log('cached: ' . $request->getRequestUri());
//				Mage::log('processor request uri: ' . $processor->getRequestUri($this, $request));
//				Mage::log('cache id: ' . $cacheId);
//				Mage::log($this->getRequestTags());
			}
		}
		else {
//			Mage::log('not cached: ' . $request->getRequestUri());
//			Mage::log($request->getRequestUri());
		}
		return $this;
	}

	/**
	 * Get specific request processor based on request parameters.
	 *
	 * @param Zend_Controller_Request_Http $request
	 * @return Df_PageCache_Model_Processor_Default
	 */
	public function getRequestProcessor(Zend_Controller_Request_Http $request)
	{
		$processor = false;
		$configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);
		if ($configuration) {
			$configuration = $configuration->asArray();
		}
		$module = $request->getModuleName();
		if (isset($configuration[$module])) {
			$model = $configuration[$module];
			$controller = $request->getControllerName();
			if (is_array($configuration[$module]) && isset($configuration[$module][$controller])) {
				$model = $configuration[$module][$controller];
				$action = $request->getActionName();
				if (is_array($configuration[$module][$controller]) && isset($configuration[$module][$controller][$action])) {
					$model = $configuration[$module][$controller][$action];
				}
			}
			if (is_string($model)) {
				$processor = df_model($model);
			}
		}
		return $processor;
	}

	/** @return bool */
	private function isDisabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					(isset($_SERVER['HTTPS']) && ('on' === $_SERVER['HTTPS']))
				||
					isset($_COOKIE['NO_CACHE'])
				||
					isset($_GET['no_cache'])
				||
					self::isDisabledByCurrentUri()
			;
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * @override     
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$uri = false;
		/**
		 * Define server HTTP HOST
		 */
		if (isset($_SERVER['HTTP_HOST'])) {
			$uri = $_SERVER['HTTP_HOST'];
		} else if (isset($_SERVER['SERVER_NAME'])) {
			$uri = $_SERVER['SERVER_NAME'];
		}
		/**
		 * Define request URI
		 */
		if ($uri) {
			if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
				$uri.= $_SERVER['HTTP_X_REWRITE_URL'];
			} else if (isset($_SERVER['REQUEST_URI'])) {
				$uri.= $_SERVER['REQUEST_URI'];
			} else if (!empty($_SERVER['IIS_WasUrlRewritten']) && !empty($_SERVER['UNENCODED_URL'])) {
				$uri.= $_SERVER['UNENCODED_URL'];
			} else if (isset($_SERVER['ORIG_PATH_INFO'])) {
				$uri.= $_SERVER['ORIG_PATH_INFO'];
				if (!empty($_SERVER['QUERY_STRING'])) {
					$uri.= $_SERVER['QUERY_STRING'];
				}
			} else {
				$uri = false;
			}
		}
		/**
		 * Define COOKIE state
		 */
		if ($uri) {
			if (isset($_COOKIE['store'])) {
				$uri = $uri.'_'.$_COOKIE['store'];
			}
			if (isset($_COOKIE['currency'])) {
				$uri = $uri.'_'.$_COOKIE['currency'];
			}
		}
		$this->_requestId	   = $uri;
		$this->_requestCacheId  = $this->prepareCacheId($this->_requestId);
		$this->_requestTags	 = array(self::CACHE_TAG);
	}	
	/** @var string */
	private $_requestCacheId;
	/** @var string */
	private $_requestId;
	/** @var string[] */
	private $_requestTags;
	const _CLASS = __CLASS__;
	const CACHE_TAG = 'FPC';
	const LAST_PRODUCT_COOKIE = 'LAST_PRODUCT';
	const NO_CACHE_COOKIE = 'NO_CACHE';
	const REQUEST_ID_PREFIX = 'REQUEST_';
	const XML_NODE_ALLOWED_CACHE = 'frontend/cache/requests';
	const XML_PATH_ALLOWED_DEPTH = 'df_speed/page_cache/allowed_depth';
	const XML_PATH_CACHE_MULTICURRENCY = 'df_speed/page_cache/multicurrency';
	const XML_PATH_LIFE_TIME = 'df_speed/page_cache/lifetime';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_PageCache_Model_Processor
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

	/** @return string */
	public static function isDisabledByCurrentUri() {
		/** @var bool $result */
		static $result;
		if (!isset($result)) {
			$result = false;
			/** @var string|null $requestUri */
			$requestUri = @$_SERVER['REQUEST_URI'];
			if ($requestUri) {
				/** @var string[] $requestUriExploded */
				$requestUriExploded = explode('?', $requestUri);
				if ($requestUriExploded) {
					/** @var string $requestPath */
					$requestPath = $requestUriExploded[0];
					/** @var string[] $noCachePaths */
					$noCachePaths = array('df-1c/cml2', 'df-yandex-market/yml');
					foreach ($noCachePaths as $noCachePath) {
						/** @var string $noCachePath */
						if (false !== strpos($requestPath, $noCachePath)) {
							$result = true;
							break;
						}
					}
				}
			}
		}
		return $result;
	}
}