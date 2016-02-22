<?php
class Df_Exline_Model_Locator extends Df_Core_Model_Abstract {
	/** @return string */
	public function getLocationIdDestination() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getLocationId(
					$this->getRequest()->getDestinationCity()
					, $this->getRequest()->getDestinationRegionName()
					, $isDestination = true
				)
			;
			if (is_null($this->{__METHOD__})) {
				$this->getRequest()->throwExceptionInvalidDestination();
			}
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	public function getLocationIdOrigin() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getLocationId(
					$this->getRequest()->getOriginCity()
					, $this->getRequest()->getOriginRegionName()
					, $isDestination = false
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function normalize($name) {return mb_strtoupper(df_trim($name));}

	/**
	 * @param array(string => string) $array
	 * @return array(string => string)
	 */
	public function normalizeArray(array $array) {
		return array_map(array($this, 'normalize'), $array);
	}

	/**
	 * @param string $locationName
	 * @param string $reqionName
	 * @param bool $isDestination
	 * @return string|null
	 */
	private function getLocationId($locationName, $reqionName, $isDestination) {
		$locationName = $this->normalize($locationName);
		$reqionName = $this->normalize($reqionName);
		/** @var string|null $result */
		$result = df_a($this->getRegionalCenters(), $locationName);
		if (
				is_null($result)
			&&
				/**
				 * EXLINE отправляет грузы только из областных центров
				 * @link https://calc.exline.kz/
				 */
				$isDestination
		) {
			foreach ($this->getPeripheralLocations() as $id => $info) {
				/** @var string $id */
				/** @var string[] $info */
				/** @var string $currentLocationName */
				$currentLocationName = df_array_first($info);
				/** @var string $currentRegionName */
				$currentRegionName = df_array_last($info);
				if (
						($locationName === $currentLocationName)
					&&
						($reqionName === $currentRegionName)
				) {
					$result = $id;
					break;
				}
			}
			if (is_null($result)) {
				$result = df_a($this->getRegions(), $reqionName);
			}
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}
	
	/** @return array(string => string[]) */
	private function getPeripheralLocations() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeValues(array(
				'NoviyAktubinskaya' => array('Новый', 'Актюбинская область')
				,'OpytniyAktubiskaya' => array('Опытный', 'Актюбинская область')
				,'KandygashAktubinskaya' => array('Кандыгаш', 'Актюбинская область')
				,'NovayaAlekseevkaAktubinskaya' => array('Новая Алексеевка', 'Актюбинская область')
				,'KargalinskoeAktubinskaya' => array('Каргалинское', 'Актюбинская область')
				,'MartukAktubinskaya' => array('Мартук', 'Актюбинская область')
				,'AlgaAktubinskaya' => array('Алга', 'Актюбинская область')
				,'HromtauAktubinskaya' => array('Хромтау', 'Актюбинская область')
				,'HobdaAktubinskaya' => array('Хобда', 'Актюбинская область')
				,'BadamshaAktubinskaya' => array('Бадамша', 'Актюбинская область')
				,'KalkamanAlmatinskaya' => array('Калкаман', 'Алматинская область')
			));
		}
		return $this->{__METHOD__};
	}
	
	/** @return array(string => string) */
	private function getRegionalCenters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeKeys(array(
				'Астана' => 'Astana'
				,'Актобе' => 'Aktobe'
				,'Актау' => 'Aktau'
				,'Алматы' => 'Almaty'
				,'Атырау' => 'Atyrau'
				,'Караганда' => 'Karaganda'
				,'Кызылорда' => 'Kyzylorda'
				,'Костанай' => 'Kostanay'
				,'Павлодар' => 'Pavlodar'
				,'Петропавловск' => 'Petropavlovsk'
				,'Тараз' => 'Taraz'
				,'Усть-Каменогорск' => 'Oskemen'
				,'Уральск' => 'Uralsk'
				,'Шымкент' => 'Shymkent'
				,'Кокшетау' => 'Kokshetau'
				,'Талдыкорган' => 'Taldykorgan'
			));
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getRegions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeKeys(array(
				'Акмолинская область' => 'Akmolynskaya'
				,'Кызылординская область' => 'Kyzylordinskaya'
				,'Южно-Казахстанская область' => 'Shymkentskaya'
			));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Shipping_Model_Rate_Request */
	private function getRequest() {return $this->cfg(self::P__REQUEST);}

	/**
	 * @param array(string => string) $map
	 * @return array(string => string)
	 */
	private function normalizeKeys(array $map) {
		return array_combine($this->normalizeArray(array_keys($map)), array_values($map));
	}
	
	/**
	 * @param array(string => string[]) $map
	 * @return array(string => string)
	 */
	private function normalizeValues(array $map) {
		return array_combine(
			array_keys($map)
			, array_map(array($this, 'normalizeArray'), array_values($map))
		);
	}	

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__REQUEST, Df_Shipping_Model_Rate_Request::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__REQUEST = 'request';
	/**
	 * @static
	 * @param Df_Shipping_Model_Rate_Request $request
	 * @return Df_Exline_Model_Locator
	 */
	public static function i(Df_Shipping_Model_Rate_Request $request) {
		return new self(array(self::P__REQUEST => $request));
	}
}