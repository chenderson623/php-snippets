<?php

/**
 * Determine if session id is valid
 *
 * @param string $session_id String to validate
 * @return boolean
 */
function isValidSessionId($session_id)
{
	return !empty($session_id) && preg_match('/^[a-zA-Z0-9]{26}$/', $session_id);
}