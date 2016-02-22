<?php
class Df_1C_Model_Cml2_Import_Processor_Product_Type_Simple
	extends Df_1C_Model_Cml2_Import_Processor_Product_Type_Simple_Abstract {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		if (!$this->getEntityOffer()->isTypeSimple()) {
			rm_1c_log(
				'Пропускаем товарное предложение «%s» как не являющееся простым товаром.'
				,$this->getEntityOffer()->getName()
			);
		}
		else {
			$this->getImporter()->import();
			/** @var Df_Catalog_Model_Product $product */
			$product = $this->getImporter()->getProduct();
			df_h()->_1c()->cml2()->reindexProduct($product);
			rm_1c_log(
				'%s товар «%s».'
				,!is_null($this->getExistingMagentoProduct()) ? 'Обновлён' : 'Создан'
				,$product->getName()
			);
			df()->registry()->products()->addEntity($product);
			df_assert(!is_null($this->getExistingMagentoProduct()));
		}
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSku() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
 			if (!is_null($this->getExistingMagentoProduct())) {
				$result = $this->getExistingMagentoProduct()->getSku();
				df_assert_sku($result);
			}
			else {
				/**
				 * Данный товар ранее не импортировался
				 * из 1С:Управление торговлей в интернет-магазин.
				 * Сначала пробуем в качестве артикула товара в интернет-магазине
				 * взять артикул товара из 1С:Управление торговлей
				 */
				/** @var string|null $externalSku */
				$externalSku = $this->getEntityProduct()->getSku();
				// У товара в 1С:Управление торговлей может отсутствовать артикул
				if (!$externalSku) {
					// У этого товара нет артикула в 1С:Управление торговлей
					rm_1c_log(
						'У товара «%s» в 1С отсутствует артикул.', $this->getEntityProduct()->getName()
					);
				}
				else {
					$externalSku = df_sku_adapt($externalSku);
					/**
					 * Убеждаемся, что ни один товар в интернет-магазине
					 * ещё не использует этот артикул
					 */
					if (!df_h()->catalog()->product()->isExist($externalSku)) {
						$result = $externalSku;
					}
					else {
						rm_1c_log('Товар с артикулом «%s» уже присутствует в магазине.', $externalSku);
					}
				}
				if (!$result) {
					/**
					 * Итак, использовать артикул 1С:Управление торговлей
					 * в качестве артикула товара в интернет-магазине мы не можем,
					 * потому что этот артикул уже занят в интернет-магазине.
					 *
					 * Поэтому пытаемся в качестве артикула товара в магазине
					 * использовать идентификатор товара в 1С:Управление торговлей.
					 */
					/** @var string $externalId */
					$externalId = $this->getEntityOffer()->getExternalId();
					/** @var string $skuCandidate */
					$skuCandidate = df_sku_adapt($externalId);
					/** @var string|null $existingProductId */
					$existingProductId = df_h()->catalog()->product()->getIdBySku($skuCandidate);
					if (is_null($existingProductId)) {
						$result = $skuCandidate;
					}
					else {
						/**
						 * Наличие в интернет-магазине товара,
						 * который использует идентификатор импортируемого сейчас товара
						 * в качестве своего артикула явно говорит о некорректном состоянии программы.
						 * Идентификатор слишком длинен, чтобы случайно повторяться!
						 */
						/** @var Df_Catalog_Model_Product $existingProduct */
						$existingProduct = df_product($existingProductId);
						df_error(
							'1С:Управление торговлей пытается передать в интернет-магазин товар «%s» '
							.'и интернет-магазин намерен присвоить ему артикул «%s», '
							.'являющийся идентификатором данного товара в 1С:Управление торговлей,'
							.'однако в интернет-магазине уже присутствует товар «%s» («%s»),'
							.'который уже использует данный идентификатор в качестве своего артикула.'
							,$this->getEntityOffer()->getName()
							,$skuCandidate
							,$existingProduct->getSku()
							,$existingProduct->getName()
						);
					}
				}
			}
			df_result_sku($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Model_Cml2_Import_Processor_Product_Type_Simple
	 */
	public static function i(Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer) {
		return new self(array(self::P__ENTITY => $offer));
	}
}