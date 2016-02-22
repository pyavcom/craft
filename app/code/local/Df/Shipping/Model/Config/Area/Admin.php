<?php
class Df_Shipping_Model_Config_Area_Admin extends Df_Shipping_Model_Config_Area_Abstract {
	/** @return int */
	public function getProcessingBeforeShippingConsiderTodayUntill() {
		return rm_nat0($this->getVar(self::KEY__VAR__PROCESSING_BEFORE_SHIPPING__CONSIDER_TODAY_UNTILL, 8));
	}

	/** @return int */
	public function getProcessingBeforeShippingDaysRaw() {
		return rm_nat0($this->getVar(self::KEY__VAR__PROCESSING_BEFORE_SHIPPING__DAYS, 1));
	}

	/** @return int */
	public function getProcessingBeforeShippingDays() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = $this->getProcessingBeforeShippingDaysRaw();
			if ($this->needConsiderDaysOffInProcessingBeforeShipping()) {
				$result +=
					df()->date()->getNumCalendarDaysByNumWorkingDays(
						$startDate =
							$this->canUseTodayForProcessing()
							? Zend_Date::now()
							: df()->date()->tomorrow()
						,$numWorkingDays = $result
						,$store = $this->getStore()
					)
				;
			}
			if (!$this->canUseTodayForProcessing()) {
				$result++;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getDeclaredValuePercent() {
		return rm_float($this->getVar(self::KEY__VAR__DECLARED_VALUE_PERCENT, 0.0));
	}

	/** @return float */
	public function getFeeFixed() {return rm_float($this->getVar(self::KEY__VAR__FEE_FIXED, 0.0));}

	/** @return float */
	public function getFeePercent() {return rm_float($this->getVar(self::KEY__VAR__FEE_PERCENT, 0.0));}

	/** @return bool */
	public function needConsiderDaysOffInProcessingBeforeShipping() {
		return $this->getVarFlag(self::KEY__VAR__PROCESSING_BEFORE_SHIPPING__CONSIDER_DAYS_OFF, false);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return self::AREA_PREFIX;}
	
	/** @return bool */
	private function canUseTodayForProcessing() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df()->date()->getHour() < $this->getProcessingBeforeShippingConsiderTodayUntill()
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return bool */
	private function isTodayOff() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					$this->needConsiderDaysOffInProcessingBeforeShipping()
				&&
					df()->date()->isTodayOff($this->getStore())
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const AREA_PREFIX = 'admin';
	const KEY__VAR__DECLARED_VALUE_PERCENT = 'declared_value_percent';
	const KEY__VAR__FEE_FIXED = 'fee_fixed';
	const KEY__VAR__FEE_PERCENT = 'fee_percent';
	const KEY__VAR__PROCESSING_BEFORE_SHIPPING__CONSIDER_DAYS_OFF = 'processing_before_shipping__consider_days_off';
	const KEY__VAR__PROCESSING_BEFORE_SHIPPING__CONSIDER_TODAY_UNTILL = 'processing_before_shipping__consider_today_untill';
	const KEY__VAR__PROCESSING_BEFORE_SHIPPING__DAYS = 'processing_before_shipping__days';
}