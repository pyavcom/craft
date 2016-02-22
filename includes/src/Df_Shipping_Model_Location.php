<?php
abstract class Df_Shipping_Model_Location extends Df_Core_Model_Abstract {
	/** @return string */
	abstract public function getRegion();

	/** @return bool */
	public function hasRegion() {return !!$this->getRegion();}

	/**
	 * @param string|string[] $name
	 * @return string
	 */
	public function normalizeName($name) {
		/** @var string|string[] $result */
		$result = null;
		/** @var bool $firstParamIsArray */
		$firstParamIsArray = is_array($name);
		/** @var mixed[] $arguments */
		$arguments = $firstParamIsArray ? $name : func_get_args();
		if ((1 < count($arguments)) || $firstParamIsArray) {
			$result = array_map(array($this, 'normalizeNameSingle'), $arguments);
		}
		else {
			$result = $this->normalizeNameSingle($name);
		}
		return $result;
	}

	/**
	 * Метод должен быть публичным, потому что он вызывается через array_map
	 * @param string $name
	 * @return string
	 */
	public function normalizeNameSingle($name) {return strtr(mb_strtoupper($name), array('Ё' => 'Е'));}

	/**
	 * @param string $name
	 * @param string[] $partsToRemove
	 * @param bool $isCaseSensitive [optional]
	 * @return string
	 */
	protected function cleanName($name, array $partsToRemove, $isCaseSensitive = false) {
		$name = $isCaseSensitive ? $name : $this->normalizeName($name);
		$partsToRemove = $isCaseSensitive ? $partsToRemove : $this->normalizeName($partsToRemove);
		return
			df_trim(
				strtr(
					$name
					,array_combine(
						$partsToRemove
						,array_fill(0, count($partsToRemove), '')
					)
				)
			)
		;
	}

	/** @return bool */
	protected function isRegionCleaningCaseSensitive() {return false;}

	/** @return string[] */
	protected function getRegionPartsToClean() {
		return array('край', 'область', 'республика', 'авт. округ', 'край', 'обл.', 'респ.');
	}

	/** @return array(string => string) */
	protected function getRegionReplacementMap() {
		return
			array(
				'Саха' => 'Саха (Якутия)'
				,'Северная Осетия' => 'Северная Осетия — Алания'
				,'Тыва' => 'Тыва (Тува)'
			)
		;
	}

	/**
	 * @param string $regionName
	 * @return string
	 */
	protected function normalizeRegionName($regionName) {
		return
			$this->replaceInName(
				$this->cleanName(
					$regionName
					,$this->getRegionPartsToClean()
					,$this->isRegionCleaningCaseSensitive()
				)
				,$this->getRegionReplacementMap()
			)
		;
	}

	/**
	 * @param string $name
	 * @param array(string => string) $replacementMap
	 * @return string
	 */
	protected function replaceInName($name, array $replacementMap) {
		return
			strtr(
				$this->normalizeName($name)
				,array_combine(
					$this->normalizeName(array_keys($replacementMap))
					,$this->normalizeName(array_values($replacementMap))
				)
			)
		;
	}

	const _CLASS = __CLASS__;
}