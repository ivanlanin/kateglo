<?php
/**
 *
 */
class random extends page
{

	var $random;
	var $limit = 10;
	var $max = 50;
	var $min_length;
	var $max_length;

	/**
	 * Constructor
	 */
	function random(&$db, &$auth, $msg)
	{
		parent::page(&$db, &$auth, $msg);
		global $_GET;
		// limit
		$limit = $_GET['limit'];
		if (is_numeric($limit))
		{
			$limit = intval($limit);
			if ($limit > 0)
			{
				$this->limit = $limit;
				if ($limit > $this->max) $this->limit = $this->max;
			}
		}
		// min length
		$min_length = $_GET['min_length'];
		if (is_numeric($min_length))
		{
			$min_length = intval($min_length);
			if ($min_length > 0) $this->min_length = $min_length;
		}
		// max length
		$max_length = $_GET['max_length'];
		if (is_numeric($max_length))
		{
			$max_length = intval($max_length);
			if ($max_length > 0) $this->max_length = $max_length;
		}
		// sanity check: if min_length > max_length, then make them equal
		if ($this->min_length && $this->max_length)
		{
			if ($this->min_length > $this->max_length)
			{
				$this->min_length = $this->max_length;
			}
		}
	}

	/**
	 *
	 */
	function process()
	{
		$random_entries = '';
		$query = 'SELECT phrase, lex_class FROM phrase
			WHERE (LEFT(phrase, 2) != \'a \' AND LEFT(phrase, 2) != \'b \')
			AND NOT ISNULL(updated) AND NOT ISNULL(lex_class)
			AND ISNULL(actual_phrase) ';
		if ($this->min_length)
			$query .= 'AND LENGTH(phrase) >= ' . $this->min_length . ' ';
		if ($this->max_length)
			$query .= 'AND LENGTH(phrase) <= ' . $this->max_length . ' ';
		$query .= 'ORDER BY RAND() LIMIT ' . $this->limit. ';';
		$result = $this->db->get_rows($query);
		foreach ($result as $key => $val)
		{
			$entry = $val['phrase'];
			$random_entries .= $random_entries ? ', ' : '';
			$random_entries .= "'" . $entry . "'";
			$random[$entry] = array('phrase' => $entry);
		}
		$query = 'SELECT def_uid, phrase, def_text FROM definition
			WHERE phrase IN (' . $random_entries . ')
			ORDER BY phrase, def_num;';
		$result = $this->db->get_rows($query);
		foreach ($result as $key => $val)
		{
			$entry = $val['phrase'];
			$random[$entry]['definition'][] = $val['def_text'];
		}
		foreach ($random as $key => $val)
		{
			$this->random[] = $val;
		}
	}

	/**
	 * Get API
	 */
	function getAPI()
	{
		return($this->random);
	}

};
?>