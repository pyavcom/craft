<?php
class Df_Tweaks_Block_Frontend_Js extends Df_Core_Block_Template {
	/**
	 * @override
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return
			array_merge(
				parent::getCacheKeyInfo()
				,array(get_class($this))
				,rm_layout()->getUpdate()->getHandles()
			)
		;
	}
	
	/** @return string */
	public function getOptionsAsJson() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Раньше тут стояло
			 * $theme = rm_design_package()->getTheme('skin');
			 * То есть, мы использовали в качестве идентификатора темы
			 * значение опции «Нестандартная папка браузерных файлов».
			 * Однако в оформительской теме Gala TitanShop в одном из демо-примеров
			 * (и в других аналогично) значением опции «Нестандартная папка браузерных файлов»
			 * является «galatitanshop_lingries_style01»,
			 * в то время как опция «Нестандартная папка темы» имеет правильное значение
			 * «galatitanshop».
			 * Поэтому вместо
			 * $theme = rm_design_package()->getTheme('skin');
			 * я решил использовать
			 * $theme = rm_design_package()->getTheme('default');
			 * Передавая в метод getTheme() параметр «default», мы извлекаем значение опции
			 * «Нестандартная папка темы».
			 */
			/** @var string $theme */
			$theme = rm_design_package()->getTheme('default');
			if (!$theme) {
				$theme = Mage_Core_Model_Design_Package::DEFAULT_THEME;
			}
			/** @var array(string => string) $options */
			$options = array(
				'package' => rm_design_package()->getPackageName()
				,'theme' => $theme
				// быстро узнать версию движка при просмотре страницы
				// нам важно для диагностики
				,'version' => array(
					'rm' => rm_version()
					,'core' => Mage::getVersion()
				)
				,'formKey' => rm_session_core()->getFormKey()
			);
			/**
			 * С другой стороны, значение опции «Нестандартная папка браузерных файлов»
			 * нам тоже может потребоваться: ведь именно значение этой опции определяет,
			 * какие файлы CSS будут загружены.
			 * Поэтому записываем в rm.tweaks.options и это значение тоже,
			 * только не в ключе «theme», а в ключе «skin».
			 */
			/** @var string $skin */
			$skin = rm_design_package()->getTheme('skin');
			if ($skin) {
				$options['skin'] = $skin;
			}
			/** @var string $result */
			$this->{__METHOD__} = df_output()->json($options);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/tweaks/js.phtml';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
		 * продолжительность хранения кэша надо указывать обязательно,
		 * потому что значением продолжительности по умолчанию является «null»,
		 * что в контексте @see Mage_Core_Block_Abstract
		 * (и в полную противоположность Zend Framework
		 * и всем остальным частям Magento, где используется кэширование)
		 * означает, что блок не удет кэшироваться вовсе!
		 * @see Mage_Core_Block_Abstract::_loadCache()
		 */
		$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
	}
	const _CLASS = __CLASS__;
}