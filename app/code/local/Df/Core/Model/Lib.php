<?php
class Df_Core_Model_Lib extends Df_Core_Model_Lib_Abstract {
	/** @var bool */
	public static $initialized = false;
	/** @return void */
	public function init() {
		if (!self::$initialized) {
			Mage::setIsDeveloperMode(true);
			ini_set('mbstring.internal_encoding', 'UTF-8');
			/**
			 * Magento CE, включая самую свежую на настоящее время версию 1.9.0.1,
			 * официально не совместима с PHP 5.4 и 5.5.
			 * Однако добиться этой совместимости просто:
			 * достаточно отключить предупреждения PHP уровня E_DEPRECATED.
			 * Такое предупреждение, в частности, возникает при вызове метода
			 * @see Mage_Core_Helper_Abstract::removeTags():
			 * «preg_replace(): The /e modifier is deprecated, use preg_replace_callback instead.»
			 */
			/**
			 * Обратите внимание, что константа @see E_DEPRECATED появилась только в PHP 5.3
			 * @link http://php.net/manual/en/errorfunc.constants.php
			 */
			if (defined('E_DEPRECATED')) {
				/**
				 * Обратите внимание, что ошибочно писать здесь
				 * error_reporting(error_reporting() ^ E_DEPRECATED);
				 * потому что ^ — это побитовое XOR,
				 * и если предыдущее значение error_reporting не содержало E_DEPRECATED,
				 * то код error_reporting(error_reporting() ^ E_DEPRECATED);
				 * добавит в error_reporting E_DEPRECATED.
				 */
				error_reporting(error_reporting() &~ E_DEPRECATED);
			}
			/**
			 * Обратите внимание, что двойной инициализации не происходит,
			 * потому что Mage::helper() ведёт реестр создаваемых объектов
			 * и создаёт единственный экземпляр конкретного класса.
			 */
			Df_Core_Helper_Lib::s();
			self::$initialized = true;
		}
		/** @var bool $timeZoneInitialized */
		static $timeZoneInitialized = false;
		if (!$timeZoneInitialized) {
			/**
			 * Нельзя вызывать Mage::getStoreConfig в режиме установки-обновления,
			 * потому что иначе система установит в качестве текущего магазина заглушку:
			 * @see Mage_Core_Model_App::getStore():
				if (!Mage::isInstalled() || $this->getUpdateMode()) {
					return $this->_getDefaultStore();
				}
			 * @see Mage_Core_Model_App::_getDefaultStore():
				$this->_store = Mage::getModel('core/store')
				   ->setId(self::DISTRO_STORE_ID)
				   ->setCode(self::DISTRO_STORE_CODE);
			 *
			 * В дальнейшем это приводит к фатальным сбоям в коде, подобном следующиему:
			 * @see Mage_Adminhtml_Catalog_ProductController::_initProductSave():
			 * $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
			 * В этом коде вызов Mage::app()->getStore(true)->getWebsite()->getId() даст сбой,
			 * потому что установочный магазин-заглушка не привязан ни к какому сайту
			 * (поле website_id не инициализировано).
			 */
			/** @var bool $useTimezoneStub */
			$useTimezoneStub = !Mage::isInstalled() || Mage::app()->getUpdateMode();
			/** @var string $timezoneStub */
			$timezoneStub = 'Europe/Moscow';
			try {
				/**
				 * Здесь может случиться исключительная ситуация,
				 * если мы попали в этот метод по событию resource_get_tablename,
				 * а магазин ещё не инициализирован.
				 * Просто игнорируем её.
				 */
				/** @var string|null $defaultTimezone */
				$defaultTimezone =
					$useTimezoneStub
					? $timezoneStub
					: Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)
				;
				/**
				 * По необъяснимой причине
				 * после предыдущего вызова $defaultTimezone может быть пустым значением
				 */
				if ($defaultTimezone) {
					date_default_timezone_set($defaultTimezone);
					if (!$useTimezoneStub) {
						$timeZoneInitialized = true;
					}
				}
			}
			catch(Exception $e) {}
		}
		/** @var bool $shutdownInitialized */
		static $shutdownInitialized = false;
		if (!$shutdownInitialized) {
			register_shutdown_function(array(Df_Qa_Model_Shutdown::_CLASS, 'processStatic'));
			$shutdownInitialized = true;
		}
	}
	/** @return Df_Core_Model_Lib */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}