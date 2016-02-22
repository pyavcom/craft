<?php
class Df_YandexMarket_Model_Settings_Products extends Df_YandexMarket_Model_Settings_Yml {
	/** @return Mage_CatalogRule_Model_Rule|null */
	public function getRule() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_CatalogRule_Model_Rule|null $result */
			if (!$this->getRuleId()) {
				$result = null;
			}
			else {
				rm_nat($this->getRuleId());
				$result = df_model('catalogrule/rule');
				$result->load($this->getRuleId());
				if (!$result->getId()) {
					df_error(
						'Не могу загрузить из базы данных ценовое правило №%d для каталога.'
						.' Может быть, администратор удалил его?'
						.' В таком случае откройте административный экран настроек модуля Яндекс.Маркет'
						.' и пересохраните эти настройки'
						.' (нажмите кнопку «сохранить настройки» в правом верхнем углу экрана).'
						,$result->getId()
					);
				}
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}
	/** @return bool */
	public function needPublishOutOfStock() {return $this->getYesNo('publish_out_of_stock');}
	/** @return int */
	private function getRuleId() {return $this->getNatural0('conditions');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/products/';}
	/** @return Df_YandexMarket_Model_Settings_Products */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}