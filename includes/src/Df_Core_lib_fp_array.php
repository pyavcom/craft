<?php
/**
 * @param array(string => mixed) $entity
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function df_a(array $entity, $key, $default = null) {
	/**
	 * Раньше функция @see df_a была универсальной:
	 * она принимала в качестве аргумента $entity как массивы, так и объекты.
	 * В 99.9% случаев в качестве параметра передавался массив.
	 * Поэтому ради ускорения работы системы
	 * вынес обработку объектов в отдельную функцию @see df_o
	 */
	return isset($entity[$key]) ? $entity[$key] : $default;
}

/**
 * @param mixed $elements
 * @return mixed[]
 */
function df_array_clean($elements) {
	$elements = is_array($elements) ? $elements : func_get_args();
	return df_clean($elements);
}

/**
 * array_combine требует, чтобы оба массива содержали не менее 1 элемента,
 * поэтому надо выделять случай с пустыми массивами в отдельную ветку алгоритма
 *
 * @param array $array1
 * @param array $array2
 * @return array
 */
function df_array_combine(array $array1, array $array2) {
	return
		(0 === count($array1))
		? array()
		: array_combine($array1, $array2)
	;
}

/**
 * @param int $start_index
 * @param int $num
 * @param mixed $value
 * @return array the filled array
 */
function df_array_fill($start_index, $num, $value) {
	return
		(0 === $num)
		? array()
		: array_fill($start_index, $num, $value)
	;
}

/**
 * Функция возвращает null, если массив пуст
 * @param array $array
 * @return mixed|null
 */
function df_array_first(array $array) {
	/**
	 * Обратите внимание, что неверен код
		$result = reset($array);
		return (false === $result) ? null : $result;
	 * потому что если @see reset() вернуло false, это не всегда означает сбой метода:
	 * ведь первый элемент массива может быть равен false.
	 */
	return !$array ? null : reset($array);
}

/**
 * Функция возвращает null, если массив пуст
 * @link http://www.php.net/manual/en/function.end.php#107733
 * @param array $array
 * @return mixed|null
 */
function df_array_last(array $array) {
	/**
	 * Если использовать end вместо df_array_last,
	 * то указатель массива после вызова end сместится к последнему элементу.
	 * При использовании df_array_last смещения указателя не происходит,
	 * потому что в df_array_last попадает лишь копия массива
	 */
	/**
	 * Обратите внимание, что неверен код
	 	$result = end($array);
	 	return (false === $result) ? null : $result;
	 * потому что если @see end() вернуло false, это не всегда означает сбой метода:
	 * ведь последний элемент массива может быть равен false.
	 */
	return !$array ? null : end($array);
}

/**
 * @param array $array
 * @return string|int|null
 */
function df_array_min(array $array) {
	/** @var string|int $result */
	$result = null;
	/** @var int|float $resultValue */
	$resultValue = null;
	foreach ($array as $key => $value) {
		/** @var string|int $key */
		/** @var mixed $value */
		if (is_null($resultValue) || ($value < $resultValue)) {
			$resultValue = $value;
			$result = $key;
		}
	}
	return $result;
}

/**
 * Эта функция аналогична @see array_merge,
 * однако если сливаемые массивы содержат числовые ключи,
 * то массив-результат слияния сохранит эти ключи
 * (стандартная функция @see array_merge
 * при слиянии массивов с числовыми ключами не сохраняет их).
 * @param array(int|string => mixed) $array1
 * @param array(int|string => mixed) $array2
 * @return array(int|string => mixed)
 */
function df_array_merge_assoc(array $array1, array $array2) {
	/** @var array(int|string => mixed) $result */
	$result = $array1;
	foreach ($array2 as $key => $value) {
		/** @var int|string $key */
		/** @var mixed $value */
		$result[$key] = $value;
	}
	return $result;
}

/**
 * Этот метод предназначен для извлечения некоторого значения
 * из многомерного массива посредством нотации ключ1/ключ2/ключ3
 *
 * @param array $array
 * @param string $query
 * @param mixed $defaultValue[optional]
 * @return mixed
 */
function df_array_query(array $array, $query, $defaultValue = null) {
	df_param_string_not_empty($query, 1);
	/** @var mixed $result */
	$result = null;
	if (!rm_contains($query, Df_Core_Const::T_XPATH_SEPARATOR)) {
		$result = df_a($array, $query, $defaultValue);
	}
	else {
		/** @var array $paramNameAsArray */
		$queryAsArray = explode(Df_Core_Const::T_XPATH_SEPARATOR, $query);
		while (0 < count($queryAsArray)) {
			$result = df_a($array, array_shift($queryAsArray));
			$array = is_null($result) ? array() : $result;
		}
		if (is_null($result)) {
			$result = $defaultValue;
		}
	}
	return $result;
}

/**
 * Отсекает первый элемент массива и возвращает хвост (аналог CDR в Lisp)
 * @param array $array
 * @return array
 */
function df_array_tail(array $array) {return array_slice($array, 1);}

/**
 * @param array $arr
 * @param string|int $key
 * @param mixed $val
 * @return int
 */
function df_array_unshift_assoc(&$arr, $key, $val)  {
	$arr = array_reverse($arr, true);
	$arr[$key] = $val;
	$arr = array_reverse($arr, true);
	return count($arr);
}

/**
 * @param array $array
 * @param array $additionalValuesToClean
 * @param null|array $keysToClean
 * @return array
 */
function df_clean(array $array, array $additionalValuesToClean = array(), $keysToClean = null) {
	if ($keysToClean) {
		$result =
			array_merge(
				array_diff_key($array, array_flip($keysToClean))
				,df_clean(
					array_intersect_key($array, array_flip($keysToClean))
					,$additionalValuesToClean
				)
			)
		;
	}
	else {
		$result = array();
		$valuesToClean = array_merge(array('', null), $additionalValuesToClean);
		$isAssoc = df_is_assoc($array);
		foreach ($array as $key => $value) {
			if (!in_array($value, $valuesToClean, true)) {
				if ($isAssoc) {
					$result[$key]= $value;
				}
				else {
					$result[]= $value;
				}
			}
		}
	}
	return $result;
}

/**
 * @param mixed[] $array
 * @param string $column
 * @return array
 */
function df_column($array, $column) {
	$result = array();
	foreach ($array as $item) {
		$result[]= df_a($item, $column);
	}
	return $result;
}

/**
 * @param array|string $method
 * @param array $array
 * @param array $params
 * @return array
 */
function df_each($method, $array, $params = array()) {
	/** @var array $result */
	$result = array();
	foreach ($array as $key => $item) {
		$result[$key] =
			call_user_func(
				array($item, $method)
				,$params
			)
		;
	}
	return $result;
}

/**
 * @param array $array
 * @param string $keyToCompare
 * @param mixed $value
 * @return array
 */
function df_filter(array $array, $keyToCompare, $value) {
	$result = array();
	$isAssoc = false; //df_is_assoc ($array);
	foreach ($array as $index => $item) {
		if ($value === df_a($item, $keyToCompare)) {
			if ($isAssoc) {
				$result[$index] = $item;
			}
			else {
				$result[]= $item;
			}
		}
	}
	return $result;
}

/**
 * @param array $array
 * @param string $key
 * @param array $values
 * @return array
 */
function df_filter_by_array(array $array, $key, array $values) {
	$result = array();
	foreach ($array as $item) {
		if (in_array(df_a($item, $key), $values)) {
			$result[]= $item;
		}
	}
	return $result;
}

/**
 * @param array|object $entity
 * @param array $keys
 * @return array|stdClass
 */
function df_filter_keys($entity, $keys) {
	$result = null;
	if (is_array($entity)) {
		$result = array();
		foreach ($keys as $key) {
			$result[$key] = df_a($entity, $key);
		}
	}
	else {
		if (is_object($entity)) {
			$result = new stdClass();
			foreach ($keys as $key) {
				$result->$key = $entity->$key;
			}
		}
	}
	return $result;
}

/**
 * @param array $array
 * @param string $key
 * @param mixed $value
 * @return mixed|null
 */
function df_find(array $array, $key, $value) {
	$index = df_find_index ($array, $key, $value);
	return
			(is_null($index))
		?
			null
		:
			df_a($array, $index)
	;
}

/**
 * @param array $array
 * @param string $attributeName
 * @param mixed $attributeValue
 * @return string|int
 */
function df_find_index(array $array, $attributeName, $attributeValue) {
	$result = null;
	foreach ($array as $index => $item) {
		$value = null;
		if (is_array($item)) {
			$value = df_a($item, $attributeName);
		}
		else {
			if (is_object($item)) {
				/**
				 * К сожалению, нельзя здесь для проверки публичности метода
				 * использовать is_callable, * потому что наличие Varien_Object::__call
				 * приводит к тому, что is_callable всегда возвращает true.
				 */
				if (method_exists($item, $attributeName)) {
					$value = call_user_func (array($item, $attributeName));
				}
				else {
					$value = df_a($item, $attributeName);
				}
			}
		}
		if ($value === $attributeValue) {
			$result = $index;
			break;
		}
	}
	return $result;
}

/**
 * @param array $array
 * @return bool
 */
function df_is_assoc(array $array) {
	$result = false;
	foreach (array_keys($array) as $key => $value) {
		/**
		 * Согласно спецификации PHP, ключами массива могут быть целые числа, либо строки.
		 * Третьего не дано.
		 * @link http://php.net/manual/en/language.types.array.php
		 */
		if (
			/**
			 * Раньше тут стояло !is_int($key)
			 * Способ проверки $key !== $value нашёл по ссылке ниже:
			 * @link http://www.php.net/manual/en/function.is-array.php#84488
			 */
			$key !== $value
		) {
			$result = true;
			break;
		}
	}
	return $result;
}

/**
 * @param string[]|string $function
 * @param array|Iterator $array
 * @param mixed $params
 * @return array|Iterator
 */
function df_map($function, $array, $params = array()) {
	return df()->fp()->map($function, $array, $params);
}

/**
 * Оба входных массива должны быть ассоциативными
 * @param array(string => mixed) $array1
 * @param array(string => mixed) $array2
 * @return array(string => mixed)
 */
function df_merge_not_empty(array $array1, array $array2) {
	/** @var array(string => mixed) $result */
	$result = $array1;
	foreach ($array2 as $key2 => $value2) {
		/** @var string $key2 */
		/** @var mixed $value2 */
		if ($value2) {
			$result[$key2] = $value2;
		}
	}
	return $result;
}

/**
 * @link http://en.wikipedia.org/wiki/Tuple
 * @param array $arrays
 * @return array
 */
function df_tuple(array $arrays) {
	/** @var array $result */
	$result = array();
	/** @var int $count */
	$countItems = max(array_map('count', $arrays));
	for($ordering = 0; $ordering < $countItems; $ordering++) {
		/** @var array $item */
		$item = array();
		foreach ($arrays as $arrayName => $array) {
			$item[$arrayName]= df_a($array, $ordering);
		}
		$result[$ordering] = $item;
	}
	return $result;
}

/**
 * @param mixed $value
 * @return array
 */
function df_wrap_in_array($value) {return is_array($value) ? $value : array($value);}

/**
 * @param mixed|mixed[] $value
 * @return mixed[]
 */
function rm_array($value) {return is_array($value) ? $value : array($value);}

/**
 * Работает в разы быстрее, чем @see array_unique()
 * @link http://stackoverflow.com/questions/5036504/php-performance-question-faster-to-leave-duplicates-in-array-that-will-be-searc#comment19991540_5036538
 * @link http://www.php.net/manual/en/function.array-unique.php#70786
 * @param mixed[] $array
 * @return mixed[]
 */
function rm_array_unique_fast(array $array) {return array_keys(array_flip($array));}

/**
 * @param string $glue
 * @param string|string[] $elements
 * @return string
 */
function rm_concat_clean($glue, $elements) {
	if (!is_array($elements)) {
		/** @var string[] $arguments */
		$arguments = func_get_args();
		$elements = df_array_tail($arguments);
	}
	return implode($glue, df_array_clean($elements));
}

/**
 * @param string|string[] $array
 * @return string|string[]
 */
function rm_uppercase($array) {
	return !is_array($array) ? mb_strtoupper($array) : array_map('mb_strtoupper', $array);
}