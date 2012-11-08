<?php

/**
 * Get list of all keys of a multidimentional array
 *
 * @param array $array Multidimensional array to extract keys from
 * @return array
 */
function array_keys_multi(array $array)
{
	$keys = array();

	foreach ($array as $key => $value) {
		$keys[] = $key;

		if (is_array($array[$key])) {
			$keys = array_merge($keys, array_keys_multi($array[$key]));
		}
	}

	return $keys;
}