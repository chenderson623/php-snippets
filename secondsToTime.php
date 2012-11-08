<?php

/**
 * Convert number of seconds into hours, minutes and seconds
 * and return an array containing those values
 *
 * @param integer $seconds Number of seconds to parse
 * @return array
 */
function secondsToTime($seconds)
{
	// extract hours
	$hours = floor($seconds / (60 * 60));

	// extract minutes
	$divisor_for_minutes = $seconds % (60 * 60);
	$minutes = floor($divisor_for_minutes / 60);

	// extract the remaining seconds
	$divisor_for_seconds = $divisor_for_minutes % 60;
	$seconds = ceil($divisor_for_seconds);

	// return the final array
	$obj = array(
		"h" => (int) $hours,
		"m" => (int) $minutes,
		"s" => (int) $seconds,
	);
	return $obj;
}