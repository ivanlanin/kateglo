<?php
/**
 * Prototype for page class
 */
class page
{
	var $db; // database object
	var $auth; // auth object
	var $msg; // messages
	var $title; // page title
	var $is_post; // post status

	/**
	 * Constructor
	 */
	function page(&$db, &$auth, $msg)
	{
		global $_SERVER;
		$this->db = $db;
		$this->auth = $auth;
		$this->msg = $msg;
		$this->is_post = ($_SERVER['REQUEST_METHOD'] == 'POST');
	}

	/**
	 * Process page
	 */
	function process()
	{
	}

	/**
	 * Show page
	 */
	function show()
	{
	}

	/**
	 * Get API data
	 */
	function getAPI()
	{
		return;
	}

	/**
	 * Keywords
	 */
	function get_keywords()
	{
		return;
	}

	/**
	 * Description
	 */
	function get_description()
	{
		return;
	}

	/**
	 * Description
	 */
	function get_action_buttons($all_actions, $available_actions = null)
	{
		// which actions are available?
		if (is_array($available_actions))
			foreach ($available_actions as $key)
				$actions[$key] = $all_actions[$key];
		else
			$actions = $all_actions;

		// show only available actions
		$ret .= '<p>' . LF;
		foreach ($actions as $key => $value)
		{
			$ret .= sprintf(
				'<a href="%2$s" class="action_button"%3$s>%1$s</a>' . LF,
				$value['label'] ? $value['label'] : $this->msg[$key],
				$value['url'],
				$value['target'] ? ' target="' . $value['target'] . '"' : ''
			);
		}
		$ret .= '</p>' . LF;
		return($ret);
	}

};
?>