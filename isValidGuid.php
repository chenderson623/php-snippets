<?php

/**
 * Determine if supplied string is a valid GUID
 *
 * @param string $guid String to validate
 * @return boolean
 */
function isValidGuid($guid)
{
	return !empty($guid) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $guid);
}