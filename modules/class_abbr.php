<?php
/**
 *
 *
 *
 */
require_once($base_dir . '/modules/class_mediawiki.php');
class abbr extends page
{

	var $entry;
	var $sublist = false;

	/**
	 * Constructor
	 */
	function abbr(&$db, &$auth, $msg)
	{
		parent::page(&$db, &$auth, $msg);
	}

	/**
	 *
	 */
	function process()
	{
		global $_GET;
		global $_SERVER;
		if ($_GET['phrase'])
			$_GET['abbr_key'] = $_GET['phrase'];
		else
			$_GET['phrase'] = $_GET['abbr_key'];

		switch ($_GET['action'])
		{
			case 'form':
				if ($this->is_post && $this->auth->checkAuth() && $_GET['action'] == 'form')
					$this->save_form();
				break;
			default:
				break;
		}
	}

	/**
	 *
	 */
	function show()
	{
		global $_GET;
		switch ($_GET['action'])
		{
			case 'form':
				$ret .= $this->show_form();
				break;
			default:
				$ret .= $this->show_main();
				break;
		}
		return($ret);
	}

	/**
	 *
	 */
	function show_main()
	{
		global $_GET;

		// title
		$this->title = $this->msg['abbr_acr'];
		$disciplines = $this->db->get_row_assoc('
			SELECT discipline, discipline_name
			FROM discipline ORDER BY discipline_name;',
			'discipline', 'discipline_name');
		if (array_key_exists($_GET['dc'], $disciplines))
			$this->title .= ' ' . $disciplines[$_GET['dc']];

		$ret .= sprintf('<h1>%1$s</h1>' . LF, $this->title);

		// new button and search
		$actions = array(
			'new' => array('url' => './' .
				$this->get_url_param(array('search', 'action', 'uid', 'mod')) .
				'&mod=abbr&action=form'
			),
		);
		if (!$this->sublist)
		{
			if ($this->auth->checkAuth())
				$ret .= $this->get_action_buttons($actions);
			$ret .= $this->show_search();
		}

		$ret .= $this->show_result();

		// return
		return($ret);
	}

	/**
	 *
	 */
	function show_result()
	{
		global $_GET;

		$operators = array(
			'1' => array('type'=>'LIKE', 'open'=>'%', 'close'=>'%'),
			'2' => array('type'=>'REGEXP', 'open'=>'[[:<:]]', 'close'=>'[[:>:]]'),
			'3' => array('type'=>'=', 'open'=>'', 'close'=>''),
			'4' => array('type'=>'LIKE', 'open'=>'', 'close'=>'%'),
			'5' => array('type'=>'LIKE', 'open'=>'%', 'close'=>''),
		);
		if (!array_key_exists($_GET['op'], $operators)) $_GET['op'] = '1';
		$op_open = $operators[$_GET['op']]['open'];
		$op_close = $operators[$_GET['op']]['close'];
		$op_type = $operators[$_GET['op']]['type'];
		$op_template = 'lower(a.%1$s) %2$s lower(\'%3$s%4$s%5$s\')';

		if ($_GET['abbr_key'])
		{
			$where .= $where ? ' AND ' : ' WHERE ';
			$where .= sprintf($op_template, 'abbr_key', $op_type, $op_open,
				$this->db->quote($_GET['abbr_key'], null, false), $op_close);
		}

		if ($_GET['abbr_id'])
		{
			$where .= $where ? ' AND ' : ' WHERE ';
			$where .= sprintf($op_template, 'abbr_id', $op_type, $op_open,
				$this->db->quote($_GET['abbr_id'], null, false), $op_close);
		}

		if ($_GET['abbr_en'])
		{
			$where .= $where ? ' AND ' : ' WHERE ';
			$where .= sprintf($op_template, 'abbr_en', $op_type, $op_open,
				$this->db->quote($_GET['abbr_en'], null, false), $op_close);
		}

		if ($_GET['tag'])
		{
			$where .= $where ? ' AND ' : ' WHERE ';
			$where .= sprintf($op_template, 'abbr_type', 'REGEXP', '[[:<:]]',
				$this->db->quote($_GET['tag'], null, false), '[[:>:]]');
		}

		$actions = array(
			'input' => array('label' => 'Usulkan penambahan/perbaikan',
			'target' => 'abbr_form',
			'url' => 'http://spreadsheets0.google.com/a/bahtera.org/viewform?hl=in&formkey=dGhDZjB4M2ZWT2hwZmVBNG80bzlkcXc6MQ'),
		);
		$ret .= $this->get_action_buttons($actions);

		if ($this->sublist) $this->db->defaults['rperpage'] = 20;
		$phrase1 = 'abbr_key';
		$cols = 'a.abbr_key, a.abbr_id, a.abbr_en, a.notes, a.url, a.abbr_idx, a.abbr_type, a.url, a.redirect_to';
		$from = 'FROM abbr_entry a
			' . $where . '
			ORDER BY ' . $phrase1;
		$rows = $this->db->get_rows_paged($cols, $from);

		if ($this->db->num_rows > 0)
		{

			// print
			$ret .= '<p>';
			$ret .= $this->db->get_page_nav();
			$ret .= '</p>' . LF;

			$ret .= '<table class="table table-condensed table-hover">' . LF;

			// header
			$ret .= '<tr>' . LF;
			$tmp = '<th width="%2$s%%">%1$s</th>' . LF;;
			$ret .= sprintf($tmp, '&nbsp;', '1');
			$ret .= sprintf($tmp, $this->msg['abbr_key'], '10');
			$ret .= sprintf($tmp, $this->msg['abbr_id'], '25');
			$ret .= sprintf($tmp, $this->msg['abbr_en'], '25');
			$ret .= sprintf($tmp, $this->msg['tag'], '10');
			$ret .= sprintf($tmp, $this->msg['notes'], '30');
			if ($this->auth->checkAuth())
				$ret .= sprintf($tmp, '&nbsp;', '1');
			$ret .= '</tr>' . LF;

			// rows
			$i = 0;
			$tmp = '<td align="%2$s"%3$s>%1$s</td>' . LF;
			// tag
			$get_all = 'mod=abbr';
//			foreach ($_GET as $get_key => $get_val)
//			{
//				if ($get_val != '' && !in_array($get_key, array('tag', 'p')))
//				{
//					$get_all .= $get_all ? '&' : '';
//					$get_all .= $get_key . '=' . $get_val;
//				}
//			}

			// looping
			foreach ($rows as $row)
			{
				$lemma[] = $row['original'];
				$uid[] = $row['abbr_idx'];
				$tags = explode(';', $row['abbr_type']);
				$tag_display = '';
				if ($tags)
				{
					foreach ($tags as $tag)
					{
						$tag = trim($tag);
						$tag_display .= $tag_display ? '; ' : '';
//						$tag_display .= '<a href="./?' . $get_all . '&tag=' . $tag . '">' . $tag . '</a>';
						$tag_display .= '<a href="./?' . $get_all . '&tag=' . $tag . '">' . $tag . '</a>';
					}
				}
				$notes = '';
				if ($row['redirect_to']) $notes .= '→ <a href="./?mod=abbr&op=3&abbr_key=' . $row['redirect_to'] . '">' . $row['redirect_to'] . '</a><br />';
				if ($row['url']) $notes .= '<a href="' . $row['url'] . '">' . $row['url'] . '</a><br />';
				if ($row['notes']) $notes .= $row['notes'];
				$url = './' . $this->get_url_param(array('search', 'action', 'uid', 'mod')) .
					'&action=form&mod=abbr&uid=' . $row['abbr_idx'];
				$ret .= '<tr valign="top">' . LF;
				$ret .= sprintf($tmp, ($this->db->pager['rbegin'] + $i) . '.', 'left', '');
				$ret .= sprintf($tmp, $row['abbr_key'], 'left', '');
				$ret .= sprintf($tmp, $row['abbr_id'], 'left', '');
				$ret .= sprintf($tmp, $row['abbr_en'], 'left', '');
				$ret .= sprintf($tmp, $tag_display, 'left', '');
				$ret .= sprintf($tmp, $notes, 'left', '');
				//$ret .= sprintf($tmp, $this->parse_keywords($row['abbr_id']), 'left', '');
				// operation
				if ($this->auth->checkAuth())
					$ret .= sprintf($tmp,
						sprintf('<a href="%1$s">%2$s</a>', $url, $this->msg['edit']), 'left', '');
				$ret .= '</tr>' . LF;
				$i++;
			}
			$ret .= '</table>' . LF;

			$ret .= '<p>';
			$ret .= $this->db->get_page_nav();
			$ret .= '</p>' . LF;
		}
		else
			$ret .= '<p>' . $this->msg['nf'] . '</p>' . LF;
		return($ret);
	}

	/**
	 *
	 */
	function show_search()
	{
		$operators = array(
			'1' => $this->msg['search_1'],
			'2' => $this->msg['search_2'],
			'3' => $this->msg['search_3'],
			'4' => $this->msg['search_4'],
			'5' => $this->msg['search_5'],
		);

		$form = new form('search_glo', 'get');
		$form->setup($msg);
		$form->addElement('hidden', 'mod', 'abbr');
		$form->addElement('text', 'abbr_key', $this->msg['abbr_key'],
			array('size' => 15, 'maxlength' => 255));
		$form->addElement('text', 'abbr_id', $this->msg['abbr_id'],
			array('size' => 15, 'maxlength' => 255));
		$form->addElement('text', 'abbr_en', $this->msg['abbr_en'],
			array('size' => 15, 'maxlength' => 255));
		$form->addElement('text', 'tag', $this->msg['tag'],
			array('size' => 15, 'maxlength' => 255));
		$form->addElement('select', 'op', null, $operators);
		$form->addElement('submit', 'srch', $this->msg['search_button']);

		$template = '<span class="search_param" style="white-space:nowrap; margin-right:20px;">%1$s: %2$s</span>' . LF;
		$ret .= $form->begin_form();
		$ret .= '<div class="panel panel-default">' . LF;
		$ret .= '<div class="panel-heading">' . $this->msg['search'] . '</div>' . LF;
		$ret .= '<div class="panel-body">' . LF;
		$ret .= $form->get_element('mod');
		$ret .= sprintf($template, $this->msg['search_op'], $form->get_element('op'));
		$ret .= sprintf($template, $this->msg['abbr_key'], $form->get_element('abbr_key'));
		$ret .= sprintf($template, $this->msg['abbr_id'], $form->get_element('abbr_id'));
		$ret .= sprintf($template, $this->msg['abbr_en'], $form->get_element('abbr_en'));
		$ret .= sprintf($template, $this->msg['tag'], $form->get_element('tag'));
		$ret .= $form->get_element('srch');
		$ret .= $form->end_form();
		$ret .= '</div>' . LF;
		$ret .= '</div>' . LF;

		return($ret);
	}

	/**
	 *
	 */
	function show_form()
	{
		$query = 'SELECT a.* FROM abbr_entry a
			WHERE a.abbr_idx = ' . $this->db->quote($_GET['uid']);
		$this->entry = $this->db->get_row($query);
		$is_new = is_array($this->entry) ? 0 : 1;

		$form = new form('entry_form', null, './' . $this->get_url_param());
		$form->setup($this->msg);
		$form->addElement('text', 'abbr_key', $this->msg['abbr_acr'], array('size' => 20, 'maxlength' => '20'));
		$form->addElement('text', 'abbr_id', $this->msg['abbr_id'], array('size' => 40, 'maxlength' => '4000'));
		$form->addElement('text', 'abbr_en', $this->msg['abbr_en'], array('size' => 40, 'maxlength' => '4000'));
		$form->addElement('text', 'abbr_type', $this->msg['phrase_type'], array('size' => 20, 'maxlength' => '255'));
		$form->addElement('text', 'lang', $this->msg['lang'], array('size' => 20, 'maxlength' => '255'));
		$form->addElement('text', 'redirect_to', $this->msg['actual_phrase'], array('size' => 20, 'maxlength' => '255'));
		$form->addElement('text', 'source', $this->msg['ref_source'], array('size' => 20, 'maxlength' => '255'));
		$form->addElement('text', 'url', $this->msg['url'], array('size' => 40, 'maxlength' => '255'));
		$form->addElement('textarea', 'notes', $this->msg['notes'], array('style' => 'width:100%'));
		$form->addElement('hidden', 'abbr_idx');
		$form->addElement('hidden', 'is_new', $is_new);
		$form->addElement('submit', 'save', $this->msg['save']);
		$form->addRule('abbr_key', sprintf($this->msg['required_alert'], $this->msg['abbr_key']), 'required', null, 'client');
		$form->setDefaults($this->entry);

		$ret .= sprintf('<h1>%1$s</h1>' . LF,
			($is_new ? $this->msg['new'] : $this->msg['edit']) .
			' - ' . $this->msg['abbr']
		);
		$ret .= $form->toHtml();
		return($ret);
	}

	/**
	 * Save glossary
	 *
	 * @return unknown_type
	 */
	function save_form()
	{
		global $_GET, $_POST;
		$is_new = ($_POST['is_new'] == 1);

		// construct query
		$query = ($is_new ? 'INSERT INTO' : 'UPDATE') . ' abbr_entry SET ';
		$query .= sprintf('
			abbr_key = %1$s,
			abbr_id = %2$s,
			abbr_en = %3$s,
			abbr_type = %4$s,
			lang = %5$s,
			redirect_to = %6$s,
			source = %7$s,
			url = %8$s,
			notes = %9$s',
			$this->db->quote($_POST['abbr_key']),
			$this->db->quote($_POST['abbr_id']),
			$this->db->quote($_POST['abbr_en']),
			$this->db->quote($_POST['abbr_type']),
			$this->db->quote($_POST['lang']),
			$this->db->quote($_POST['redirect_to']),
			$this->db->quote($_POST['source']),
			$this->db->quote($_POST['url']),
			$this->db->quote($_POST['notes'])
			);
		if (!$is_new)
			$query .= sprintf(' WHERE abbr_idx = %1$s;',
				$this->db->quote($_POST['abbr_idx'])
			);

		// die($query);
		$this->db->exec($query);

		// redirect
		$_GET['abbr_key'] = $_POST['abbr_key'];
		redir('./' . $this->get_url_param(array('action', $_GET['abbr_key'])));
	}

	/**
	 *
	 */
	function get_url_param($exclude = null)
	{
		global $_GET;
		$ret = '';
		foreach ($_GET as $key => $val)
		{
			$is_excluded = false;
			$is_excluded = (trim($val) == '');
			if ($exclude)
				if (in_array($key, $exclude))
					$is_excluded = true;
			if (!$is_excluded)
			{
				$ret .= $ret ? '&' : '?';
				$ret .= $key . '=' . $val;
			}
		}
		if (!$ret) $ret = '?';
		return($ret);
	}

	/**
	 * Valid: alphanum and underscore
	 */
	function parse_keywords($string)
	{
		$keywords = preg_split("/[^\w]+/", $string);
		$clean_key = array();
		foreach($keywords as $word)
		{
			$word = trim($word);
			if ($word && !in_array($word, $clean_key))
			{
				$clean_key[] = $word;
			}
		}
		sort($clean_key);
		// cleaned key
		$url = '<a href="./?mod=dictionary&action=view&phrase=%1$s">%1$s</a>';
		foreach($clean_key as $word)
		{
			{
				$keyword .= $keyword ? '; ' : '';
				$keyword .= sprintf($url, $word);
			}
		}
		return($keyword);
	}

	/**
	 *
	 */
	function get_wikipedia($wp, $lemma, $idx, &$rows)
	{
		global $is_offline;
		if ($is_offline) return;

		$mw = new mediawiki($wp);
		$pages = $mw->get_page_info($lemma);
		$i = 0;
		if (!is_array($pages)) return;

		foreach ($pages as $key => $page)
		{
			if ($page['status'] == 1)
			{
				// UIDs
				$uids = '';
				foreach ($idx[$key] as $uid)
				{
					$uids .= $uids ? ', ' : '';
					$uids .= $rows[$uid]['abbr_idx'];
				}

				// english wikipedia
				$query = sprintf(
					'UPDATE abbr_entry SET wp%3$s = %1$s WHERE abbr_idx IN (%2$s);',
					$this->db->quote($page['to']),
					$uids,
					$wp
				);
				$this->db->exec($query);
				foreach ($idx[$key] as $uid)
					$rows[$uid]['wp' . $wp] = $page['to'];
			}
			$i++;
		}
	}
};
?>