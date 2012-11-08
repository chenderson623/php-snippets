<?php

/**
 * Generate string of random characters
 *
 * @param integer $length  Length of the string to generate
 * @param integer $lower   Include lower case characters
 * @param integer $upper   Include uppercase characters
 * @param integer $nums    Include numbers
 * @param integer $special Include special characters
 * @return string
 */
function getRandomCharString($length, $lower = true, $upper = true, $nums = true, $special = false)
{
	$pool_lower = 'abcdefghijklmopqrstuvwxyz';
	$pool_upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$pool_nums = '0123456789';
	$pool_special = '!$%^&*+#~/|';
	
	$pool = '';
	$res = '';
	
	if ($lower === true) {
		$pool .= $pool_lower;
	}
	if ($upper === true) {
		$pool .= $pool_upper;
	}
	if ($nums === true) {
		$pool .= $pool_nums;
	}
	if ($special === true) {
		$pool .= $pool_special;
	}
	
	if (($length < 0) || ($length == 0)) {
		return $res;
	}
	
	srand((double) microtime() * 1000000);
	
	for ($i = 0; $i < $length; $i++) {
		$charidx = rand() % strlen($pool);
		$char = substr($pool, $charidx, 1);
		$res .= $char;
	}
	
	return $res;
}