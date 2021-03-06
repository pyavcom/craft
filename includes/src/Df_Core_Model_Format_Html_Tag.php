<?php
class Df_Core_Model_Format_Html_Tag extends Df_Core_Model_Abstract {
	/** @return string */
	public function getOutput() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				strtr(
					!$this->getContent() && $this->isShortTagAllowed()
					? '<{tag-and-attributes}/>'
					: '<{tag-and-attributes}{after-attributes}>{content}</{tag}>'
					,array(
						'{tag}' => $this->getTag()
						,'{tag-and-attributes}' => $this->getOpenTagWithAttributesAsText()
						,'{after-attributes}' => $this->shouldAttributesBeMultiline() ? "\r\n" : ''
						,'{content}' => $this->getContent()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	public function getAttributes() {return $this->cfg(self::P__ATTRIBUTES, array());}

	/**
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	public function getAttributeAsText($name, $value) {
		df_param_string($name, 0);
		df_param_string($value, 1);
		return
			implode(
				'='
				,array(
					$name
					,rm_sprintf(
						'\'%s\''
						,str_replace(
							'\''
							,'\\\''
							,$value
						)
					)
				)
			)
		;
	}

	/** @return string */
	private function getAttributesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				implode(
					$this->shouldAttributesBeMultiline() ? "\n" :  ' '
					,array_map(
						array($this, 'getAttributeAsText')
						,array_keys($this->getAttributes())
						,array_values($this->getAttributes())
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getContent() {
		/** @var string $result */
		$result = $this->cfg(self::P__CONTENT, '');
		$result = df_trim($result, "\n");
		/** @var bool $isMultiline */
		$isMultiline = rm_contains($result, "\n");
		if ($isMultiline) {
			$result =
				rm_sprintf(
					"\n%s\n"
					,df_tab_multiline($result)
				)
			;
		}
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getOpenTagWithAttributesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_concat_clean(' '
				,$this->getTag()
				,$this->shouldAttributesBeMultiline() ? "\n" : null
				,call_user_func(
					$this->shouldAttributesBeMultiline() ? 'df_tab_multiline' : 'df_nop'
					,$this->getAttributesAsText()
				)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getTag() {return $this->cfg(self::P__TAG);}

	/** @return bool */
	public function isShortTagAllowed() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !in_array(strtolower($this->getTag()), array('div', 'script'));
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function shouldAttributesBeMultiline() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (1 < count($this->getAttributes()));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__TAG, self::V_STRING_NE)
			->_prop(self::P__CONTENT, self::V_STRING, false)
			->_prop(self::P__ATTRIBUTES, self::V_ARRAY, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__ATTRIBUTES = 'attributes';
	const P__CONTENT = 'content';
	const P__TAG = 'tag';

	/**
	 * @param string $href
	 * @return string
	 */
	public static function cssExternal($href) {
		df_param_string_not_empty($href, 0);
		return self::output(
			'', 'link', array('rel' => 'stylesheet', 'type' => 'text/javascript', 'href' => $href
		));
	}

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Format_Html_Tag
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

	/**
	 * @param string $content
	 * @param string $tag
	 * @param array $attributes[optional]
	 * @return string
	 */
	public static function output($content, $tag, array $attributes = array()) {
		df_param_string($content, 0);
		df_param_string($tag, 1);
		df_param_array($attributes, 2);
		return self::i(array(
			self::P__ATTRIBUTES => $attributes
			,self::P__CONTENT => $content
			,self::P__TAG => $tag
		))->getOutput();
	}

	/**
	 * @param string $src
	 * @return string
	 */
	public static function scriptExternal($src) {
		df_param_string_not_empty($src, 0);
		return self::output('', 'script', array('type' => 'text/javascript', 'src' => $src));
	}

	/**
	 * @param string $code
	 * @return string
	 */
	public static function scriptLocal($code) {
		df_param_string_not_empty($code, 0);
		return self::output($code, 'script', array('type' => 'text/javascript'));
	}
}