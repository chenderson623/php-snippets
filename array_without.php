<?php

/**
 * Remove specified values from the array
 *
 * @param array $array  Array to update
 * @param array $values Array of values to remove
 * @return array
 */
function array_without(array $array, $values)
{
	$values = (array) $values;
	
	foreach ($values as $value) {
		do {
			$key = array_search($value, $array);
			
			if ($key !== false) {
				unset($array[$key]);
			}
		} while ($key !== false);
	}
	
	return $array;
}