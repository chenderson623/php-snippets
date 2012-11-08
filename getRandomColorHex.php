<?php
 
/**
 * Get random color hex value
 *
 * @param integer $max_r Maximum value for the red color
 * @param integer $max_g Maximum value for the green color
 * @param integer $max_b Maximum value for the blue color
 * @return string
 */
function getRandomColorHex($max_r = 255, $max_g = 255, $max_b = 255)
{
	// ensure that values are in the range between 0 and 255
	if ($max_r > 255) { $max_r = 255; }
	if ($max_g > 255) { $max_g = 255; }
	if ($max_b > 255) { $max_b = 255; }
	if ($max_r < 0) { $max_r = 0; }
	if ($max_g < 0) { $max_g = 0; }
	if ($max_b < 0) { $max_b = 0; }
	
	// generate and return the random color
	return str_pad(dechex(rand(0, $max_r))), 2, '0', STR_PAD_LEFT) .
	       str_pad(dechex(rand(0, $max_g))), 2, '0', STR_PAD_LEFT) .
	       str_pad(dechex(rand(0, $max_b))), 2, '0', STR_PAD_LEFT);
}