<?php
class Df_YandexMarket_Model_Setup_2_17_33 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		self::attribute()->addAdministrativeCategoryAttribute(
			$attributeId = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			,$attributeLabel = 'Категория Яндекс.Маркета'
		);
		/**
		 * Раньше тут был код (не совсем идеальный):
				self::attribute()->addAdministrativeAttribute(
					$entityType = 'catalog_product'
					,$attributeId = Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
					,$attributeLabel = 'Категория Яндекс.Маркета'
				);
		 * Теперь улучшенный вариант этого кода я поместил в инсталлятор версии 2.38.2.
		 * Прежний код удалил, потому что есть возможность удалить его безболезненно
		 * и тем самым избавиться от необходимости его сопровождения.
		 *
		 * Прежний код был не совсем идеален по той причине,
		 * что @see Df_Catalog_Model_Resource_Installer_Attribute::addAdministrativeAttribute()
		 * добавляет свойство сразу ко всем текущим прикладным типам товара,
		 * но никак не решает задачу добавления этого свойства
		 * к программно создаваемым в будущем прикладным типам товара
		 * (программно типы товара создают на 2014-09-29 модули 1С и МойСклад).
		 *
		 * Обратите внимание, что создаваемых вручную администратором прикладных типов товара
		 * эта проблема не касалась, потому что вручную прикладные типы
		 * всегда создаются на основе какого-либо уже существующего прикладного типа
		 * и наследуют все свойства этого прикладного типа
		 * (в том числе и добавленные нами свойства).
		 */
		/**
		 * Вот в таких ситуациях, когда у нас меняется структура прикладного типа товаров,
		 * нам нужно сбросить глобальный кэш EAV.
		 */
		rm_eav_reset();
		Df_Catalog_Model_Category::reindexFlat();
	}

	/**
	 * @buyer {buyer}
	 * @return Df_YandexMarket_Model_Setup_2_17_33
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}