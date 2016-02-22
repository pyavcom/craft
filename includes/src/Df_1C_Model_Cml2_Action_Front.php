<?php
class Df_1C_Model_Cml2_Action_Front extends Df_1C_Model_Cml2_Action {
	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		if (
				Df_1C_Model_Cml2_InputRequest_Generic::MODE__CHECK_AUTH
			===
				$this->getRmRequest()->getMode()
		) {
			$this->action_login();
		}
		else {
			$this->checkLoggedIn();
			if (
					Df_1C_Model_Cml2_InputRequest_Generic::MODE__INIT
				===
					$this->getRmRequest()->getMode()
			) {
				$this->action_init();
			}
			else {
				if (
						Df_1C_Model_Cml2_InputRequest_Generic::TYPE__CATALOG
					===
						$this->getRmRequest()->getType()
				) {
					switch($this->getRmRequest()->getMode()) {
						/**
						 * Этот режим имеется в версии 4.0.2.3 модуля 1С-Битрикс для обмена с сайтом:
							Процедура ДобавитьПараметрыПротоколаОбменаВСтруктуру(СтруктураПараметров)
								СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_Инициализация"			, "&mode=init");
								СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ПередачаФайла"			, "&mode=file&filename=");
								СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ИмпортФайлаСервером"		, "&mode=import&filename=");
								СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ПолучитьДанные"			, "&mode=query");
								СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_УспешноеЗавершениеИмпорта", "&mode=success");
								СтруктураПараметров.Вставить("ПараметрЗапросаHTTP_ДеактивацияДанныхПоДате"	, "&mode=deactivate");
								(...)
							КонецПроцедуры
						 * @link http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
						 * Что он означает — пока неясно: надо смотреть исходники последних версий 1С-Битрикс.
						 * В журнале 1С этот режим прокомментирован так:
						 * «Деактивация элементов, не попавшие в полную пакетную выгрузку.»
						 */
						case Df_1C_Model_Cml2_InputRequest_Generic::MODE__DEACTIVATE:
							$this->action_catalogDeactivate();
							break;
						case Df_1C_Model_Cml2_InputRequest_Generic::MODE__FILE:
							$this->action_catalogUpload();
							break;
						case Df_1C_Model_Cml2_InputRequest_Generic::MODE__IMPORT:
							$this->action_catalogImport();
							break;
					}
				}
				else if (
						Df_1C_Model_Cml2_InputRequest_Generic::TYPE__ORDERS
					===
						$this->getRmRequest()->getType()
				) {
					switch($this->getRmRequest()->getMode()) {
						case Df_1C_Model_Cml2_InputRequest_Generic::MODE__QUERY:
							$this->action_ordersExport();
							break;
						case Df_1C_Model_Cml2_InputRequest_Generic::MODE__SUCCESS:
							$this->action_ordersExportSuccess();
							break;
						case Df_1C_Model_Cml2_InputRequest_Generic::MODE__FILE:
							$this->action_ordersImport();
							break;
					}
				}
			}
		}
	}

	/** @return void */
	private function action_catalogDeactivate() {
		Df_1C_Model_Cml2_Action_Catalog_Deactivate::i($this->getData())->process();
	}

	/** @return void */
	private function action_catalogImport() {
		Df_1C_Model_Cml2_Action_Catalog_Import::i($this->getData())->process();
	}

	/** @return void */
	private function action_catalogUpload() {
		Df_1C_Model_Cml2_Action_Catalog_Upload::i($this->getData())->process();
	}

	/** @return void */
	private function action_init() {
		Df_1C_Model_Cml2_Action_Init::i($this->getData())->process();
	}

	/** @return void */
	private function action_login() {
		Df_1C_Model_Cml2_Action_Login::i($this->getData())->process();
	}

	/** @return void */
	private function action_ordersExport() {
		Df_1C_Model_Cml2_Action_Orders_Export::i($this->getData())->process();
	}

	/** @return void */
	private function action_ordersExportSuccess() {
		$this->setResponseBodyAsArrayOfStrings(array('success', ''));
	}

	/** @return void */
	private function action_ordersImport() {
		Df_1C_Model_Cml2_Action_Orders_Import::i($this->getData())->process();
	}

	/** @return Df_1C_Model_Cml2_Action_Front */
	private function checkLoggedIn() {
		/** @var string|null $sessionId */
		$sessionId = Df_1C_Model_Cml2_Cookie::s()->getSessionId();
		if (!df_check_string_not_empty($sessionId)) {
			df_error(
				'1С должна была указать в запросе идентификатор сессии, однако не указала.'
				. "\r\nОбработка запроса невозможна."
				. "\r\nОбратитесь к программисту."
			);
		}
		$this->getSessionMagentoAPI()->setSessionId($sessionId);
		if (!$this->getSessionMagentoAPI()->isLoggedIn($sessionId)) {
			df_error(
				'Доступ к данной операции запрещён,'
				. ' потому что система не смогла распознать администратора (неверная сессия)'
			);
		}
		return $this;
	}

	/**
	 * @static
	 * @param Df_1C_Cml2Controller $controller
	 * @return Df_1C_Model_Cml2_Action_Front
	 */
	public static function i(Df_1C_Cml2Controller $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}