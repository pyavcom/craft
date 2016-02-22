<?php
class Df_Adminhtml_Helper_Data extends Mage_Adminhtml_Helper_Data {
	/**
	 * @param Mage_Adminhtml_Controller_Action $controller
	 * @return string
	 */
	public function getTranslatorByController(Mage_Adminhtml_Controller_Action $controller) {
		/** @var string $controllerClass */
		$controllerClass = get_class($controller);
		if (!isset($this->{__METHOD__}[$controllerClass])) {
			/** @var array $classNameParts */
			$classNameParts =
				explode(
					Df_Core_Model_Reflection::PARTS_SEPARATOR
					,$controllerClass
				)
			;
			if ('Mage' !== df_a($classNameParts, 0)) {
				$result =
					df()->reflection()->getModuleName(
						$controllerClass
					)
				;
			}
			else {
				$result =
					implode(
						Df_Core_Model_Reflection::PARTS_SEPARATOR
						,array(
							df_a($classNameParts, 0)
							,df_a($classNameParts, 2)
						)
					)
				;
				/**
				 * Однако же, данного модуля может не существовать.
				 * Например, для адреса http://localhost.com:656/index.php/admin/system_design/
				 * алгоритм возвращает название несуществующего модуля «Mage_System».
				 *
				 * В таком случае возвращаемся к алторитму из первой ветки
				 * (по сути, для стандартного кода возвращаем «Mage_Adminhtml»)
				 */

				if (!df_module_enabled($result)) {
					$result =
						df()->reflection()->getModuleName(
							$controllerClass
						)
					;
				}
			}
			df_result_string($result);
			$this->{__METHOD__}[$controllerClass] = $result;
		}
		return $this->{__METHOD__}[$controllerClass];
	}

	/** @return string */
	private function getModuleNameForTranslation() {
		if (!isset($this->{__METHOD__})) {
			if (
					df_enabled(Df_Core_Feature::LOCALIZATION)
				&&
					df_cfg()->localization()->translation()->admin()->isEnabled()
				&&
					(rm_state()->getController() instanceof Mage_Adminhtml_Controller_Action)
			) {
				/** @var Mage_Adminhtml_Controller_Action $controller */
				$controller = rm_state()->getController();
				$this->{__METHOD__} = df_h()->adminhtml()->getTranslatorByController($controller);
			}
			else {
				$this->{__METHOD__} = self::DF_PARENT_MODULE;
			}
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const DF_PARENT_MODULE = 'Mage_Adminhtml';

	/** @return Df_Adminhtml_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}