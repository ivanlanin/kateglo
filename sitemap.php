<?php
/**
 * Sitemap generator
 */
$maps = array(
	'home' => '',
	'dictionary' => '/?mod=dict',
	'glossary' => '/?mod=glo',
	'readme' => '/?mod=doc&doc=README.txt',
	'comment' => '/?mod=comment',
);
$url = 'http://%1$s/kateglo%2$s';
foreach ($maps as $key => $page)
{
	$ret .= sprintf($url, $_SERVER['SERVER_NAME'], $page) . "\n";
}
echo($ret);
?>