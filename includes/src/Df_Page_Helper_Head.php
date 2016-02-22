<?php
class Df_Page_Helper_Head extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $type
	 * @param string $name
	 * @return bool
	 */
	public function needSkipItem($type, $name) {
		/** @var bool $result */
		$result =
				$this->needSkipAsJQuery($type, $name)
			||
				$this->needSkipAsStandardCss($type, $name)
		;
		return $result;
	}

	/**
	 * @param string $scriptName
	 * @return bool
	 */
	private function isItJQuery($scriptName) {
		/**
		 * Как ядро библиотеки jQuery должны определяться скрипты с именами следующего вида:
		 * path/jquery.js
		 * path/jquery-1.8.3.js
		 * path/jquery-1.8.3.min.js
		 *
		 * Обратите внимание, что скрипты с именами вроде path/history.adapter.jquery.js
		 * не должны определяться, как ядро библиотеки jQuery.
		 * @link http://magento-forum.ru/topic/3979/
		 */
		/** @var string $fileName */
		$fileName = df_array_last(explode('/', $scriptName));
		/** @var string $pattern */
		$pattern = '#^jquery(\-\d+\.\d+\.\d+)?(\.min)?\.js$#ui';
		/** @var string[] $matches */
		$matches = array();
		$result = (1 === preg_match($pattern, $fileName, $matches));
		return $result;
	}

	/**
	 * @param string $scriptName
	 * @return bool
	 */
	private function isItJQueryNoConflict($scriptName) {
		return rm_contains(mb_strtolower($scriptName), mb_strtolower('noconflict.js'));
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @return bool
	 */
	private function needSkipAsJQuery($type, $name) {
		/** @var bool $jqueryRemoveExtraneous */
		static $jqueryRemoveExtraneous;
		if (!isset($jqueryRemoveExtraneous)) {
			$jqueryRemoveExtraneous =
					(
							df_is_admin()
						&&
							df_cfg()->admin()->jquery()->needRemoveExtraneous()
						&&
							(
									Df_Admin_Model_Config_Source_JqueryLoadMode::VALUE__NO_LOAD
								!==
									df_cfg()->admin()->jquery()->getLoadMode()
							)
					)
				||
					(
							!df_is_admin()
						&&
							df_cfg()->tweaks()->jquery()->needRemoveExtraneous()
						&&
							(
									Df_Admin_Model_Config_Source_JqueryLoadMode::VALUE__NO_LOAD
								!==
									df_cfg()->tweaks()->jquery()->getLoadMode()
							)
					)

			;
		}
		/** @var bool $result */
		$result =
				$jqueryRemoveExtraneous
			&&
				(in_array($type, array('js', 'skin_js')))
			&&
				/**
				 * Обратите внимание, что Российская сборка Magento добавляет на страницу
				 * библиотеку jQuery не посредством addItem, а более низкоуровневыми методами
				 */
				(
						$this->isItJQuery($name)
					||
						$this->isItJQueryNoConflict($name)
				)
		;
		return $result;
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @return bool
	 */
	private function needSkipAsStandardCss($type, $name) {
		/** @var bool $needSkipStandardCss */
		static $needSkipStandardCss;
		if (!isset($needSkipStanradsCss)) {
			$needSkipStandardCss =
				rm_bool(
					(string)Mage::getConfig()->getNode(
						'df/page/skip_standard_css/' . rm_state()->getController()->getFullActionName()
					)
				)
			;
		}
		/** @var bool $result */
		$result =
				$needSkipStandardCss
			&&
				(in_array($type, array('skin_css', 'js_css')))
			&&
				!rm_contains($name, 'df/')
		;
		return $result;
	}

	/** @return Df_Page_Helper_Head */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}