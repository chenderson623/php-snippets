<?php

/**
 * Compress contents of a CSS file
 *
 * @param string $css Contents of a css file
 * @return string
 */ 
function compressCss($css)
{
	// remove multiline comments, new lines, tabs and single line comments
	$css = preg_replace_callback('/(\/\*.*?\*\/|\n|\t|\/\/.*?\n)/sim',
		create_function(
			'$matches',
			'return "";'
		), $css);

	// remove all around in ",", ":" and "{"
	$css = preg_replace_callback('/\s?(,|{|:){1}\s?/sim',
		create_function(
			'$matches',
			'return $matches[1];'
		), $css);

	return $css;
}