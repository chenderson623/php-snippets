<?php

/**
 * Determine if supplied string is a valid GUID
 *
 * @param string $md5 String to validate
 * @return boolean
 */
function isValidMd5($md5)
{
	return !empty($md5) && preg_match('/^[a-f0-9]{32}$/', $md5);
}