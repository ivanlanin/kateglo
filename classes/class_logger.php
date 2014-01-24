<?php
/**
 * Common log function wrapper
 *
 * @author ivan@lanin.org
 */
class logger
{
	var $db;
	var $auth;
	var $ses_id;

	function logger(&$db, &$auth)
	{
		$this->db = $db;
		$this->auth = $auth;
		$this->ses_id = session_id();
	}

	function log()
	{
		global $_SERVER, $_GET;
		return;

		$agent = $_SERVER['HTTP_USER_AGENT'];

		// exceptions
		if (strpos($agent, 'Googlebot')) return;
		if (strpos($agent, 'CC Metadata Scaper')) return;

		// log session
		$query = sprintf('INSERT INTO sys_session (ses_id, ip_address,
			user_id, user_agent, started) VALUES (\'%1$s\', \'%2$s\', \'%3$s\', \'%4$s\', NOW());',
			$this->ses_id, $_SERVER['REMOTE_ADDR'],
			$this->auth->getUsername(), $_SERVER['HTTP_USER_AGENT']
			);
		$this->db->exec($query);
		// log page view
		$query = sprintf('INSERT INTO sys_action (ses_id, action_time,
			description) VALUES (\'%1$s\', NOW(), \'%2$s\');',
			$this->ses_id, $_SERVER['QUERY_STRING']);
		$this->db->exec($query);
		// update session data
		$query = sprintf('UPDATE sys_session
			SET last = NOW(), page_view = page_view + 1, user_id = \'%2$s\'
			WHERE ses_id = \'%1$s\';',
			$this->ses_id, $this->auth->getUsername()
			);
		$this->db->exec($query);

		// log searched phrase
		if ($_GET['phrase'])
		{
			$_GET['phrase'] = trim($_GET['phrase']);
			$query = sprintf('INSERT INTO searched_phrase (phrase)
				VALUES (\'%1$s\');', $_GET['phrase']);
			$this->db->exec($query);
			$query = sprintf('UPDATE searched_phrase SET
				search_count = search_count + 1, last_searched = NOW()
				WHERE phrase = %1$s;',
				$this->db->quote($_GET['phrase']));
			$this->db->exec($query);
		}
	}
}
?>