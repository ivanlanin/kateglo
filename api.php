<?php
/**
 * API entry point
 */

// base dir
$base_dir = dirname(__FILE__);
ini_set('include_path', $base_dir . '/pear/');

// includes
require_once($base_dir . '/config/settings.php');
require_once($base_dir . '/config/config.php');
require_once($base_dir . '/config/messages.php');
require_once('common.php');
require_once($base_dir . '/classes/class_db.php');
require_once($base_dir . '/classes/class_page.php');

// initialization
$db = new db;
$db->connect($dsn);
$db->msg = $msg;

// define mod
$mods = array(
	'dictionary',
	'random',
);
$_GET['mod'] = strtolower($_GET['mod']);
if ($_GET['mod'] == 'dict') $_GET['mod'] = 'dictionary'; // backward
if (!in_array($_GET['mod'], $mods)) $_GET['mod'] = 'dictionary';
$mod = $_GET['mod'];

// shortcut
$_GET['action'] = 'view';
$_GET['format'] = ($_GET['format'] == 'json') ? 'json' : 'xml';

// process
require_once($base_dir . '/modules/class_' . $mod . '.php');
$page = new $mod(&$db, &$auth, $msg);
$page->process();
if ($apiData = $page->getAPI())
{
	$ret = ($_GET['format'] == 'json') ? outputJSON($apiData) : outputXML($apiData);
}
else
{
	$ret = '<p>Antarmuka pemrograman aplikasi (API) yang (masih) sangat sederhana ini dibuat untuk memungkinkan para pengembang memanfaatkan data yang disediakan oleh Kateglo. Untuk tahap awal, baru modul kamus yang dapat diakses dengan API ini.</p>
	<p>Gunakan format</p>
	<blockquote>http://bahtera.org/kateglo/api.php?format=[xml|json]&phrase=[lema_yang_dicari].</blockquote></p>
	<p>Contoh:</p>
	<blockquote><a href="api.php?format=xml&phrase=kata">http://bahtera.org/kateglo/api.php?format=xml&phrase=kata</a><br /><a href="api.php?format=json&phrase=bahtera">http://bahtera.org/kateglo/api.php?format=json&phrase=bahtera</a></blockquote>
	<p>Silakan pelajari sendiri dulu keluaran XML atau JSON yang dihasilkan karena dokumentasi masih belum sempat dibuat.</p>
	<p>API ini disediakan dengan apa adanya, dan ada kemungkinan akan berubah format.</p>';
}
echo($ret);

/**
 * output XML
 */
function outputXML(&$apiData)
{
	$ret .= '<?xml version="1.0"?>' . LF;
	$ret .= '<kateglo status="1">' . LF;
	$ret .= arrayToXML(&$apiData);
	$ret .= '</kateglo>' . LF;
	header('Content-type: text/xml');
	return($ret);
}

/**
 * output JSON
 */
function outputJSON(&$apiData)
{
	$data = array('kateglo'=>$apiData);
	$ret .= json_encode($data);
	header('Content-type: application/json');
	return($ret);
}

/**
 * Array to XML
 */
function arrayToXML(&$array)
{
	foreach ($array as $key => $value)
	{
		$keyName = is_numeric($key) ? 'elm' . $key : $key;
		if (!is_array($value))
		{
			$ret .= sprintf('<%1$s>%2$s</%1$s>', $keyName, $value) . LF;
		}
		else
		{
			$ret .= sprintf('<%1$s>', $keyName) . LF;
			$ret .= arrayToXML(&$value);
			$ret .= sprintf('</%1$s>', $keyName) . LF;
		}
	}
	return($ret);
}
?>