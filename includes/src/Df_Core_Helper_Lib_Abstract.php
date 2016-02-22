<?php
class Df_Core_Helper_Lib_Abstract extends Mage_Core_Helper_Abstract {
	/**
	 * @override
	 * @return Df_Core_Helper_Lib_Abstract
	 */
	public function __construct() {
		$this->includeScripts();
		set_include_path(
			/**
			 * PATH_SEPARATOR — это символ «;» для Windows и «:» для Unix,
			 * он разделяет пути к известным интерпретатору PHP папкам со скриптами.
			 * @link http://stackoverflow.com/questions/9769052/why-is-there-a-path-separator-constant
			 */
			get_include_path() . PATH_SEPARATOR . df_path()->removeTrailingSlash($this->getLibPath())
		);
		return $this;
	}

	/** @return Df_Core_Helper_Lib_Abstract */
	public function restoreErrorReporting() {
		if (isset($this->_errorReporting)) {
			error_reporting($this->_errorReporting);
		}
		return $this;
	}

	/** @return Df_Core_Helper_Lib_Abstract */
	public function setCompatibleErrorReporting() {
		$this->_errorReporting = error_reporting();
		/**
		 * Обратите внимание, что ошибочно использовать ^ вместо &~,
		 * потому что ^ — это побитовое XOR,
		 * и если предыдущее значение error_reporting не содержало getIncompatibleErrorLevels(),
		 * то вызов с оператором ^ наоборот добавит в error_reporting getIncompatibleErrorLevels().
		 */
		error_reporting($this->_errorReporting &~ $this->getIncompatibleErrorLevels());
		return $this;
	}
	/** @var int */
	private $_errorReporting;

	/** @return int */
	protected function getIncompatibleErrorLevels() {
		return 0;
	}

	/** @return string[] */
	protected function getScriptsToInclude() {
		return array();
	}

	/**
	 * @param string $libName
	 * @return Df_Core_Helper_Lib_Abstract
	 */
	protected function includeScript($libName) {
		require_once $this->getScriptPath($libName);
		return $this;
	}

	/** @return string */
	private function getCompiledLibPath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				! defined ('COMPILER_INCLUDE_PATH')
				? ''
				:
					/**
					 * df_concat_path здесь использовать ещё нельзя,
					 * потому что библиотеки Российской сборки ещё не загружены
					 */
					implode(
						DS
						,array(
							COMPILER_INCLUDE_PATH
							,str_replace('_', DS, $this->_getModuleName())
							,'lib'
						)
					).DS
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getCurrentInstanceFileName() {
		if (!isset($this->{__METHOD__})) {
			/** @var ReflectionClass $reflectionClass */
			$reflectionClass = new ReflectionClass(get_class($this));
			$this->{__METHOD__} = $reflectionClass->getFileName();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLibPath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				defined('COMPILER_INCLUDE_PATH') && is_dir($this->getCompiledLibPath())
				? $this->getCompiledLibPath()
				: $this->getStandardLibPath()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Path to module relative to app/code, e.g.: core/Mage/Catalog
	 * @return string
	 */
	private function getModuleLocalPath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_path()->adjustSlashes(
					/**
					 * df_concat_path здесь использовать ещё нельзя,
					 * потому что библиотеки Российской сборки ещё не загружены
					 */
					implode(
						DS
						,array_slice(
							explode(
								DS
								,str_replace(
									Mage::getRoot()
									,''
									,realpath(
										dirname(
											$this->getCurrentInstanceFileName()
										)
									)
								)
							)
							,2
							,3
						)
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $libName
	 * @return string
	 */
	private function getScriptPath($libName) {return $this->getLibPath() . $libName . '.php';}

	/** @return string */
	private function getStandardLibPath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				/**
				 * df_concat_path здесь использовать ещё нельзя,
				 * потому что библиотеки Российской сборки ещё не загружены
				 */
				implode(
					DS
					,array(
						BP
						,'app', 'code', 'local'
						,str_replace('_', DS, $this->_getModuleName())
						,'lib'
					)
				) . DS
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Helper_Lib_Abstract */
	private function includeScripts() {
		$this->setCompatibleErrorReporting();
		foreach ($this->getScriptsToInclude() as $script) {
			/** @var string $script */
			$this->includeScript($script);
		}
		$this->restoreErrorReporting();
		return $this;
	}
}