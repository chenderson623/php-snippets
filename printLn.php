<?php

/**
 * Print text with a new line character appended
 *
 * @param string $text Text to print
 */
function printLn($text, $indent_tabs = 0) {
	print str_repeat("\t", $indent_tabs) . $text . "\n";
}