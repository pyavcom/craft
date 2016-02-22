<?php
class Df_PageCache_Model_Processor_Default
{
	/**
	 * Get request uri based on HTTP request uri and visitor session state
	 *
	 * @param Df_PageCache_Model_Processor $processor
	 * @param Zend_Controller_Request_Http $request
	 * @return string
	 */
	public function getRequestUri(Df_PageCache_Model_Processor $processor, Zend_Controller_Request_Http $request)
	{
		return $processor->getRequestId();
	}

	/**
	 * Check if request can be cached
	 * @param Zend_Controller_Request_Http $request
	 * @return bool
	 */
	public function allowCache(Zend_Controller_Request_Http $request)
	{
		if (rm_session_core()->getNoCacheFlag()) {
			return false;
		}
		/**
		 * Page can't be cached if customer is logged in
		 */
		if (rm_session_customer()->isLoggedIn()) {
			return false;
		}
		return true;
	}

	/**
	 * Prepare response body before caching
	 *
	 * @param Zend_Controller_Response_Http $response
	 * @return string
	 */
	public function prepareContent(Zend_Controller_Response_Http $response) {
		/**
		 * Перед помещением страницы в кэш выкидываем из страницы блоки,
		 * которые были предварительно помечены как неподлежащие кэшированию.
		 * Например, так помечен блок голосования в pagecache.xml:
			<reference name="right.poll">
				<action method="setFrameTags">
		  			<start>!--[POLL--</start><end>!--POLL]--</end>
		  		</action>
			</reference>
		 */
		$content = $response->getBody();
		/** @var string[][] $tags */
		$tags = array();
		/**
		 * Заменили (.*?) на ([^\]\>]*?), чтобы пропустить условные комментарии CSS.
		 * Проблема возникла с конструкцией:
		   <!--[if lte IE 6]>
		 		<script src="js/ie6/warning.js"></script><script>
		  			window.onload=function(){e("js/ie6/")}
		  		</script>
		   <![endif]-->
		 * @link http://magento-forum.ru/topic/2282/
		 */
		preg_match_all("/<!--\[([^\]\>]*?)-->/i", $content, $tags, PREG_PATTERN_ORDER);
		$tags = rm_array_unique_fast($tags[1]);
		foreach ($tags as $tag) {
			$contentNew =
				preg_replace(
					"/<!--\[{$tag}-->(.*?)<!--{$tag}\]-->/ims"
					,''
					,$content
				)
			;
			df_assert(
				!is_null($contentNew)
				,rm_sprintf(
					'Модулю «Полностраничное кэширование» не удалось обработать метатег «%s». Обратитесь к программисту.'
					,$tag
				)
			);
			$content = $contentNew;
		}
		return $content;
	}
}