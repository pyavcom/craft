;(function($) {
	rm.namespace('rm.tweaks');
	// rm.tweaks.options отсутствует на страницах формы ПД-4
	if (!rm.tweaks.options) {
		rm.tweaks.options = {};
	}
	//noinspection JSValidateTypes
	rm.tweaks.ThemeDetector = {
		initialized: 'rm.tweaks.ThemeDetector.initialized'
		,construct: function(_config) { var _this = {
			init: function() {
				$.each(_this.getDictionary(), function(themeCssId, themeConditions) {
					if (
							(themeConditions.package === rm.tweaks.options.package)
						&&
							(
									!rm.defined(themeConditions.theme)
								||
									(themeConditions.theme === rm.tweaks.options.theme)
								||
									(
											$.isArray(themeConditions.theme)
										&&
											(-1 !== $.inArray(rm.tweaks.options.theme, themeConditions.theme))
									)
							)
					) {
						$('body').addClass(themeCssId);
						return false;
					}
				});
				$(window).trigger({
					/** @type {String} */
					type: rm.tweaks.ThemeDetector.initialized
				});
			}
			,/**
			 * @returns {Object}
			 */
			getDictionary: function() {
				return {
					'df-theme-8theme-blanco': {package: 'default', 'theme': 'blanco'}
					,'df-theme-8theme-gadget': {package: 'default', 'theme': 'gadget'}
					,'df-theme-8theme-mercado': {package: 'mercado', 'theme': 'default'}
					,'df-theme-argento': {package: 'argento'}
					,'df-theme-cattheme-se105': {package: 'default', 'theme': 'se105'}
					/**
					 * EM Marketplace
					 * @link http://www.emthemes.com/premium-magento-themes/em-marketplace.html
					 * @link http://magento-forum.ru/forum/312/
					 */
					,'df-theme-em-marketplace': {package: 'default', 'theme': 'em0067'}
					/**
					 * ThemeForest Gala TitanShop
					 * @link http://themeforest.net/item/responsive-magento-theme-gala-titanshop/8202636
					 * @link http://magento-forum.ru/forum/352/
					 */
					,'df-theme-gala-titanshop': {package: 'default', 'theme': 'galatitanshop'}
					/**
					 * ThemeForest Infortis Fortis
					 * @link http://themeforest.net/item/fortis-responsive-magento-theme/1744309
					 * @link http://magento-forum.ru/forum/350/
					 */
					,'df-theme-infortis-fortis': {package: 'fortis', 'theme': 'default'}
					,'df-theme-infortis-ultimo': {package: 'ultimo', 'theme': 'default'}
					,'df-theme-koolthememaster-caramel': {package: 'default', 'theme': 'caramel'}
					,'df-theme-magento-default': {package: 'default', 'theme': 'default'}
					,'df-theme-magento-enterprise': {package: 'enterprise', 'theme': 'default'}
					,'df-theme-magento-modern': {package: 'default', 'theme': 'modern'}
					,'df-theme-magento-rwd': {package: 'rwd', 'theme': 'default'}
					,'df-theme-sns-xsport': {package: 'default', 'theme': 'sns_xsport'}
					,'df-theme-templatemela-beauty': {package: 'default', 'theme': 'MAG080119'}
					/**
					 * TemplateMela Classy Shop (MAG090171)
					 * @link http://www.templatemela.com/classyshop-magento-theme.html
					 * @link http://themeforest.net/item/classy-shop-magento-responsive-template/519426
					 * @link http://magento-forum.ru/forum/342/
					 */
					,'df-theme-templatemela-classy-shop': {package: 'default', 'theme': 'MAG090171'}
					/**
					 * TemplateMela (ThemeForest) Fancy Shop
					 * @link http://themeforest.net/item/fancy-shop-magento-template/3087093
					 * @link http://magento-forum.ru/forum/316/
					 */
					,'df-theme-templatemela-fancyshop':
						{package: 'default', 'theme': ['fancyshop_brown', 'fancyshop_blue', 'forest_fancyshop']}
					/**
					 * TemplateMela Minimal Multi Purpose (MAG090180)
					 * @link http://www.templatemela.com/minimal-multi-purpose-magento-theme.html
					 * @link http://magento-forum.ru/forum/341/
					 */
					,'df-theme-templatemela-minimal-multi-purpose': {package: 'default', 'theme': 'MAG090180'}
					,'df-theme-templatemonster-34402': {package: 'default', 'theme': 'theme043k'}
					,'df-theme-templatemonster-37419': {package: 'default', 'theme': 'theme264'}
					,'df-theme-templatemonster-41220': {package: 'default', 'theme': 'theme411'}
					,'df-theme-templatemonster-43442': {package: 'default', 'theme': 'theme464'}
					,'df-theme-templatemonster-45035': {package: 'default', 'theme': 'theme500'}
					/**
					 * TemplateMonster #49198 («Men's Underwear»)
					 * @link http://www.templatemonster.com/magento-themes/49198.html
					 * @link http://magento-forum.ru/forum/340/
					 */
					,'df-theme-templatemonster-49198': {package: 'default', 'theme': 'theme611'}
					,'df-theme-tt-theme069': {package: 'tt', 'theme': 'theme069'}
				};
			}
		}; _this.init(); return _this; }
	};
})(jQuery);