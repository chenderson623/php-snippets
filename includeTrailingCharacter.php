<?php


/**
 * Ensure that the string ends with the specified character
 *
 * @param string $string String to validate
 * @return string
 */
function includeTrailingCharacter($string, $character)
{
	if (strlen($string) > 0) {
		if (substr($string, -1) !== $character) {
			return $string . $character;
		} else {
			return $string;
		}
	} else {
		return $character;
	}
}


/**
 * Ensure that the string ends with forward slash
 *
 * @param string $string String to validate
 * @return string
 */
function includeTrailingForwardSlash($string)
{
	return includeTrailingCharacter($string, '/');
}


/**
 * Ensure that the string ends with backslash
 *
 * @param string $string String to validate
 * @return string
 */
function includeTrailingBackslash($string)
{
	return includeTrailingCharacter($string, '\\');
}


/**
 * Ensure that the string ends with system specific directory separator
 *
 * @param string $string String to validate
 * @return string
 */
function includeTrailingDirectorySeparator($string)
{
	return includeTrailingCharacter($string, DIRECTORY_SEPARATOR);
}