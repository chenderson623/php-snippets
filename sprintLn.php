<?php

/**
 * Return text with a new line character appended
 *
 * @param string $text  Text to print
 * @return string $text Text with \n appended
 */
function sprintLn($text, $indent_tabs = 0) {
	return str_repeat("\t", $indent_tabs) . $text . "\n";
}