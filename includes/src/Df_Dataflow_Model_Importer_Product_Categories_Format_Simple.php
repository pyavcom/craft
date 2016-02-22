<?php
class Df_Dataflow_Model_Importer_Product_Categories_Format_Simple
	extends Df_Dataflow_Model_Importer_Product_Categories_Parser {
	/**
	 * Can process the following formats:
	 * Category1, Category2
	 * Category1/Category2, Category3
	 * Escapes:
	 * \/ => /
	 * \, => , *
	 * @return array
	 */
	public function getPaths() {
		return
			df_clean(
				array_map(
					array($this, 'explodeCategoryPath')
					, $this->getCategoriesPathsAsStrings()
				)
			)
		;
	}

	/**
	 * @param string $pathAsString
	 * @return array
	 */
	public function explodeCategoryPath($pathAsString) {
		$result =
			array_map(
				"df_trim"
				,explode(
					"/"
					,str_replace(
						$this->getSlashEscaped()
						,$this->getUniqueMarker()
						,$pathAsString
					)
				)
			)
		;
		foreach ($result as &$string) {
			$string =
				str_replace(
					$this->getUniqueMarker()
					,"/"
					,$string
				)
			;
		}
		return $result;
	}

	/** @return array */
	private function getCategoriesPathsAsStrings() {
		$result =
			array_map(
				"df_trim"
				,explode(
					","
					,str_replace(
						$this->getCommaEscaped()
						,$this->getUniqueMarker()
						,$this->getImportedValue()
					)
				)
			)
		;
		foreach ($result as &$string) {
			$string =
				str_replace(
					$this->getUniqueMarker()
					,","
					,$string
				)
			;
		}
		return $result;
	}

	/** @return string */
	private function getSlashEscaped() {
		return '\/';
	}

	/** @return string */
	private function getCommaEscaped() {
		return '\,';
	}

	/** @return string */
	private function getUniqueMarker() {
		return '###';
	}
	/** @return Df_Dataflow_Model_Importer_Product_Categories_Format_Simple */
	public static function i() {return new self;}
}
