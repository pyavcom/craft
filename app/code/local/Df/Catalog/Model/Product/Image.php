<?php
class Df_Catalog_Model_Product_Image extends Mage_Catalog_Model_Product_Image {
	/**
	 * @override
	 * @return Df_Catalog_Model_Product_Image
	 */
	public function saveFile() {
		parent::saveFile();
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded =
					df_enabled(Df_Core_Feature::SEO)
				&&
					df_cfg()->seo()->images()->getAddExifToJpegs()
			;
		}
		if ($patchNeeded) {
			Df_Seo_Model_Product_Gallery_Processor_Image_Exif::i(
				array(
					Df_Seo_Model_Product_Gallery_Processor_Image_Exif
						::P__PRODUCT => $this->getProductDf()
					,Df_Seo_Model_Product_Gallery_Processor_Image_Exif
						::P__IMAGE_PATH => $this->getNewFile()
				)
			)->process();
		}
		return $this;
	}

	/** @return Mage_Catalog_Model_Product */
	private function getProductDf() {
		return $this->_productDf;
	}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @return Df_Catalog_Helper_Image
	 */
	public function setProductDf(Mage_Catalog_Model_Product $product) {
		$this->_productDf = $product;
		return $this;
	}

	/**
	 * @var Mage_Catalog_Model_Product
	 */
	private $_productDf;
}