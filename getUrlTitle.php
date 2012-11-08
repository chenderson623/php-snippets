<?php

function getUrlTitle($url)
{
	$paged = fopen($url, "r" );
	$x = 0;
	$page = '';
	
	if ($paged) {
		while (!feof($paged) && ($x < 2000)) {
			$page .= fread($paged, 8192);
			$x++;
		}
		fclose($paged);
	}
	
	preg_match("/<title>[\n\r\s]*(.*)[\n\r\s]*<\/title>/", $page, $title);

	if (isset($title[1])) {
		if ($title[1] == '') {
			return $url;
		}
	
		$title = $title[1];
		return trim($title);
	} else {
		return $url;
	}
}