<?php
class Df_PageCache_Model_Config_Backend_Cron extends Df_Admin_Model_Config_Backend_Cron {
	/**
	 * @override
	 * @return string
	 */
	protected function getCronJobName() {
		/** @var string $result */
		$result = self::RM__CRON_JOB_NAME;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getFrequencyConfigFieldName() {
		/** @var string $result */
		$result = self::RM__FREQUENCY_CONFIG_FIELD_NAME;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getFrequencyConfigGroupName() {
		/** @var string $result */
		$result = self::RM__FREQUENCY_CONFIG_GROUP_NAME;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTimeConfigFieldName() {
		/** @var string $result */
		$result = self::RM__TIME_CONFIG_FIELD_NAME;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTimeConfigGroupName() {
		/** @var string $result */
		$result = self::RM__TIME_CONFIG_GROUP_NAME;
		df_result_string($result);
		return $result;
	}

	const RM__CRON_JOB_NAME = 'df_page_cache_crawler';
	const RM__FREQUENCY_CONFIG_FIELD_NAME = 'auto_crawling__frequency';
	const RM__FREQUENCY_CONFIG_GROUP_NAME = 'page_cache';
	const RM__TIME_CONFIG_FIELD_NAME = 'auto_crawling__time';
	const RM__TIME_CONFIG_GROUP_NAME = 'page_cache';
}