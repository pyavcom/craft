<?php
class Df_Core_Model_Url_Rewrite extends Mage_Core_Model_Url_Rewrite {
	/** @return string|null */
	public function getRequestPath() {
		/** @var string|null $result */
		$result = $this->_getData(self::P__REQUEST_PATH);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/**
	 * @param string $path
	 * @return Mage_Core_Model_Url_Rewrite
	 */
	public function loadByRequestPath($path) {
		return
			parent::loadByRequestPath(
					!(df_enabled(Df_Core_Feature::SEO) && df_cfg()->seo()->urls()->getPreserveCyrillic())
				?
					$path
				:
					(
							!is_array($path)
						?
							rawurldecode($path)
						:
							array_map(
								"rawurldecode"
								,$path
							)
					)
			)
		;
	}

	/**
	 * @param string $requestPath
	 * @return Df_Core_Model_Url_Rewrite
	 */
	public function setRequestPath($requestPath) {
		df_param_string($requestPath, 0);
		$this->setData(self::P__REQUEST_PATH, $requestPath);
		return $this;
	}

	/**
	 * @param string $idPath
	 * @return Df_Core_Model_Url_Rewrite
	 */
	public function setIdPath($idPath) {
		df_param_string($idPath, 0);
		$this->setData(self::P__ID_PATH, $idPath);
		return $this;
	}

	/**
	 * @param string $targetPath
	 * @return Df_Core_Model_Url_Rewrite
	 */
	public function setTargetPath($targetPath) {
		df_param_string($targetPath, 0);
		$this->setData(self::P__TARGET_PATH, $targetPath);
		return $this;
	}
	const _CLASS = __CLASS__;
	const P__ID = 'url_rewrite_id';
	const P__ID_PATH = 'id_path';
	const P__IS_SYSTEM = 'is_system';
	const P__OPTIONS = 'options';
	const P__REQUEST_PATH = 'request_path';
	const P__STORE_ID = 'store_id';
	const P__TARGET_PATH = 'target_path';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Url_Rewrite
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Core_Model_Url_Rewrite
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}