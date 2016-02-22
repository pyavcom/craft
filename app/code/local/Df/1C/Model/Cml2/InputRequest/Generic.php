<?php
class Df_1C_Model_Cml2_InputRequest_Generic extends Df_Core_Model_InputRequest {
	/** @return string */
	public function getMode() {
		/** @var string $result */
		$result = $this->getParam('mode');
		if (
			!in_array(
				$result
				,array(
					self::MODE__CHECK_AUTH
					,self::MODE__DEACTIVATE
					,self::MODE__FILE
					,self::MODE__IMPORT
					,self::MODE__INIT
					,self::MODE__QUERY
					,self::MODE__SUCCESS
				)
			)
		) {
			df_error(
				'Недопустимое значение параметра «mode»: «%s»'
				,$result
			);
		}
		return $result;
	}

	/** @return string */
	public function getType() {
		/** @var string $result */
		$result = $this->getParam('type');
		if (
			!in_array(
				$result
				,array(
					self::TYPE__ORDERS
					,self::TYPE__CATALOG
				)
			)
		) {
			df_error(
				'Недопустимое значение параметра «type»: «%s»'
				,$result
			);
		}
		return $result;
	}

	/**
	 * Используется из @see Df_1C_Model_Cml2_Action::getRmRequestClass()
	 */
	const _CLASS = __CLASS__;
	const MODE__CHECK_AUTH = 'checkauth';
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
	const MODE__DEACTIVATE = 'deactivate';
	const MODE__FILE = 'file';
	const MODE__IMPORT = 'import';
	const MODE__INIT = 'init';
	const MODE__QUERY = 'query';
	const MODE__SUCCESS = 'success';
	const TYPE__ORDERS = 'sale';
	const TYPE__CATALOG = 'catalog';
}