<?php

function parseComment($text)
{  
	$text = strip_tags($text);
	$pattern = '/(((ht|f)tp(s?):\/\/)|(www\.[^ \[\]\(\)\n\r\t]+)|(([012]?[0-9]{1,2}\.){3}[012]?[0-9]{1,2})\/)([^ \[\]\(\),;"\'<>\n\r\t]+)([^\. \[\]\(\),;"\'<>\n\r\t])|(([012]?[0-9]{1,2}\.){3}[012]?[0-9]{1,2})/i';
	
	$text = preg_replace_callback($pattern,
		create_function(
			'$matches',
			'return getUrlTitle($matches[0]);'
		), $text);
	
	$text = nl2br($text);
	return $text;
}