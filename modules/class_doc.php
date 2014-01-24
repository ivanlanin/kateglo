<?php
/**
 *
 *
 *
 */
class doc extends page
{

	/**
	 * Constructor
	 */
	function doc(&$db, &$auth, $msg)
	{
		parent::page(&$db, &$auth, $msg);
	}

	/**
	 *
	 */
	function process()
	{
	}

	/**
	 *
	 */
	function show()
	{
		global $_GET;
		$file_name = $_GET['doc'];
		$file_url = './docs/' . $file_name;
		if (file_exists($file_url))
			$ret = nl2br(htmlentities(file_get_contents($file_url)));
		$this->title = $file_name;
		return($ret);
	}
};
?>