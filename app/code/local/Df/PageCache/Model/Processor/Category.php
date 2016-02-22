<?php
class Df_PageCache_Model_Processor_Category extends Df_PageCache_Model_Processor_Default {
	protected $_paramsMap = array(
		'display_mode'  => 'mode','limit_page'	=> 'limit','sort_order'	=> 'order','sort_direction'=> 'dir',);

	/**
	 * Get request uri based on HTTP request uri and visitor session state
	 *
	 * @param Df_PageCache_Model_Processor $processor
	 * @param Zend_Controller_Request_Http $request
	 * @return string
	 */
	public function getRequestUri(Df_PageCache_Model_Processor $processor, Zend_Controller_Request_Http $request)
	{
		$requestId = $processor->getRequestId();
		$params = $this->_getSessionParams();
		$queryParams = $request->getQuery();
		$queryParams = array_merge($params, $queryParams);
		ksort($queryParams);
		$origQuery= http_build_query($request->getQuery());
		$newQuery = http_build_query($queryParams);
		if ($origQuery) {
			$requestId = str_replace($origQuery, $newQuery, $requestId);
		} else {
			if ($newQuery) {
				$requestId = $requestId . '?' . $newQuery;
			}
		}
		return $requestId;
	}

	/**
	 * Check if request can be cached
	 * @param Zend_Controller_Request_Http $request
	 * @return bool
	 */
	public function allowCache(Zend_Controller_Request_Http $request)
	{
		$res = parent::allowCache($request);
		if ($res) {
			$params = $this->_getSessionParams();
			$queryParams = $request->getQuery();
			$queryParams = array_merge($queryParams, $params);
			$maxDepth = Mage::getStoreConfig(Df_PageCache_Model_Processor::XML_PATH_ALLOWED_DEPTH);
			$res = count($queryParams)<=$maxDepth;
		}
		return $res;
	}

	/**
	 * Get page view related parameters from session mapped to wuery parametes
	 * @return array
	 */
	protected function _getSessionParams()
	{
		$params = array();
		$data = df_mage()->catalog()->sessionSingleton()->getData();
		foreach ($this->_paramsMap as $sessionParam => $queryParam) {
			if (isset($data[$sessionParam])) {
				$params[$queryParam] = $data[$sessionParam];
			}
		}
		return $params;
	}
}