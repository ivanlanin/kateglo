<?php
/**
 *
 *
 *
 */
require_once($base_dir . '/modules/class_mediawiki.php');
class glossary extends page
{

	var $entry;
	var $sublist = false;

	/**
	 * Constructor
	 */
	function glossary(&$db, &$auth, $msg)
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
		$this->title = $this->msg['glossary'];
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
				'&mod=glossary&action=form'
			),
		);
		if (!$this->sublist)
		{
			if ($this->auth->checkAuth())
				$ret .= $this->get_action_buttons($actions);
			$ret .= $this->show_search();
		}

		// if there's phrase
		if ($_GET['phrase'] || $_GET['dc'] || $_GET['src'] || $_GET['srch'])
			$ret .= $this->show_result();
		// nothing, show main page
		else
		{
			$is_main = true;
			$ret .= sprintf('<p class="text-center"><a href="%1$s" class="btn btn-primary">%2$s</a></p>' . LF,
				'./?mod=glossary&srch=all', $this->msg['all_entry']);

            $ret .= '<dl class="dl-horizontal">';

			$ret .= '<dt>' . $this->msg['glo_by_discipline'] . '</dt>' . LF;
			$rows = $this->db->get_rows('SELECT * FROM discipline ORDER BY discipline;');
			if ($row_count = $this->db->num_rows)
			{
				$ret .= '<dd>' . LF;
				$i = 0;
				foreach ($rows as $row)
				{
					if ($i > 0) $ret .= '; ' . LF;
					$ret .= sprintf('<a href="./?mod=glossary&dc=%2$s">%1$s</a>',
						$row['discipline_name'], $row['discipline'], $row['glossary_count']);
					$i++;
				}
				$ret .= LF. '</dd>' . LF;
			}
			$ret .= '<dt>' . $this->msg['glo_by_source'] . '</dt>' . LF;
			$rows = $this->db->get_rows('SELECT * FROM ref_source WHERE glossary = 1 ORDER BY ref_source_name;');
			if ($row_count = $this->db->num_rows)
			{
				$ret .= '<dd>' . LF;
				$i = 0;
				foreach ($rows as $row)
				{
					if ($i > 0) $ret .= '; ' . LF;
					$ret .= sprintf('<a href="./?mod=glossary&src=%2$s">%1$s</a>',
						$row['ref_source_name'], $row['ref_source'], $row['glossary_count']);
					$i++;
				}
				$ret .= LF. '</dd>' . LF;
			}
		}

		// return
		return($ret);
	}

	/**
	 *
	 */
	function show_result()
	{
		global $_GET;

		$phrase = trim($_GET['phrase']);
		$discipline = trim($_GET['dc']);
		$src = trim($_GET['src']);
		$lang = trim($_GET['lang']);
		$msg1 = ($lang == 'id') ? 'id' : 'en';
		$msg2 = ($lang == 'id') ? 'en' : 'id';
		$phrase1 = ($lang == 'id') ? 'phrase' : 'original';
		$phrase2 = ($lang == 'id') ? 'original' : 'phrase';
		$wp1 = 'wp' . $msg1;
		$wp2 = 'wp' . $msg2;
        if ($this->sublist) {
            $_GET['op'] = '2';
        }
		if ($phrase)
		{
			$where .= $where ? ' AND ' : ' WHERE ';

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
			$op_template = 'a.%1$s %2$s \'%3$s%4$s%5$s\'';
			$lang_id = sprintf($op_template, 'phrase', $op_type, $op_open, $this->db->quote($phrase, null, false), $op_close);
			$lang_en = sprintf($op_template, 'original', $op_type, $op_open, $this->db->quote($phrase, null, false), $op_close);
			switch ($lang)
			{
				case 'en':
					$where .= $lang_en;
					break;
				case 'id':
					$where .= $lang_id;
					break;
				default:
					$where .= ' (' . $lang_id . ' OR ' . $lang_en . ') ';
					break;
			}
		}
		if ($discipline)
		{
			$where .= $where ? ' AND ' : ' WHERE ';
			$where .= ' a.discipline = \'' . $discipline . '\' ';
		}
		if ($src)
		{
			$where .= $where ? ' AND ' : ' WHERE ';
			$where .= ' a.ref_source = \'' . $src . '\' ';
		}
		if ($this->sublist) $this->db->defaults['rperpage'] = 20;
		$cols = 'a.original, a.phrase, b.discipline_name, a.glo_uid, a.discipline, a.ref_source, a.wpid, a.wpen, a.wikipedia_updated';
		$from = 'FROM glossary a
			LEFT JOIN discipline b ON a.discipline = b.discipline
			LEFT JOIN ref_source c ON a.ref_source = c.ref_source
			' . $where . '
			ORDER BY ' . $phrase1;
		$rows = $this->db->get_rows_paged($cols, $from);

		if ($this->db->num_rows > 0)
		{
			// get wikipedia definition, only do it when no definition
			$i = 0;
			$wp_uid = '';
			foreach ($rows as $row)
			{
				if (!$row['wikipedia_updated'])
				{
					if (!$row['wpen'])
					{
						$lemma_en[] = $row['original'];
						$idx_en[$row['original']][] = $i;
					}
					if (!$row['wpid'])
					{
						$lemma_id[] = $row['phrase'];
						$idx_id[$row['phrase']][] = $i;
					}
					$wp_uid .= $wp_uid ? ', ' : '';
					$wp_uid .= $row['glo_uid'];
				}
				$i++;
			}
			if ($wp_uid)
			{
				$this->get_wikipedia('en', $lemma_en, $idx_en, &$rows);
				$this->get_wikipedia('id', $lemma_id, $idx_id, &$rows);
				$query = 'UPDATE glossary SET wikipedia_updated = NOW() WHERE glo_uid IN (' . $wp_uid . ');';
				$this->db->exec($query);
			}

			// print
            if (!$this->sublist) {
                $ret .= '<p>';
                $ret .= $this->db->get_page_nav();
                $ret .= '</p>' . LF;
            }

			$ret .= '<table class="table table-condensed table-hover">' . LF;

			// header
			$ret .= '<tr>' . LF;
			$tmp = '<th width="%2$s%%">%1$s</th>' . LF;;
			$ret .= sprintf($tmp, '&nbsp;', '1');
			$ret .= sprintf($tmp, $this->msg[$msg1], '25');
			$ret .= sprintf($tmp, $this->msg[$msg2], '25');
			$ret .= sprintf($tmp, $this->msg['keyword'], '30');
			$ret .= sprintf($tmp, $this->msg['discipline'], '10');
			$ret .= sprintf($tmp, $this->msg['ref_source'], '10');
			if ($this->auth->checkAuth())
				$ret .= sprintf($tmp, '&nbsp;', '1');
			$ret .= '</tr>' . LF;

			// rows
			$i = 0;
			$tmp = '<td align="%2$s"%3$s>%1$s</td>' . LF;;
			foreach ($rows as $row)
			{
				$lemma[] = $row['original'];
				$uid[] = $row['glo_uid'];
				$url = './' . $this->get_url_param(array('search', 'action', 'uid', 'mod')) .
					'&action=form&mod=glossary&uid=' . $row['glo_uid'];
				$discipline = './' . $this->get_url_param(array('search', 'uid', 'dc')).
					'&dc=' . $row['discipline'];
				$ret .= '<tr valign="top">' . LF;
				$ret .= sprintf($tmp, ($this->db->pager['rbegin'] + $i) . '.', 'left', '');
				if ($row[$wp1])
					$ret .= sprintf($tmp, sprintf('<a href="http://%2$s.wikipedia.org/wiki/%3$s">%1$s</a>', $row[$phrase1], $msg1, $row[$wp1]), 'left', '');
				else
					$ret .= sprintf($tmp, $row[$phrase1], 'left', '');
				if ($row[$wp2])
					$ret .= sprintf($tmp, sprintf('<a href="http://%2$s.wikipedia.org/wiki/%3$s">%1$s</a>', $row[$phrase2], $msg2, $row[$wp2]), 'left', '');
				else
					$ret .= sprintf($tmp, $row[$phrase2], 'left', '');
				$ret .= sprintf($tmp, $this->parse_keywords($row['phrase']), 'left', '');
				if ($_GET['dc'])
					$ret .= sprintf($tmp, $row['discipline_name'], 'center', 'nowrap="nowrap"');
				else
					$ret .= sprintf($tmp, sprintf('<a href="%1$s">%2$s</a>', $discipline, $row['discipline_name']), 'center', 'nowrap="nowrap"');
				$ret .= sprintf($tmp, $row['ref_source'], 'center', '');
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
		else {
            if (!$this->sublist) {
			    $ret = '<p>' . $this->msg['nf'] . '</p>' . LF;
            }
        }
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

		$form = new form('search_glo', 'get', './', array('class' => 'form-inline'));
		$form->setup($msg);
		$form->addElement('hidden', 'mod', 'glossary');
		$form->addElement('text', 'phrase', $this->msg['phrase'],
			array('size' => 15, 'maxlength' => 255));
		$form->addElement('select', 'dc', $this->msg['discipline'],
			$this->db->get_row_assoc('SELECT discipline, discipline_name
				FROM discipline ORDER BY discipline_name',
				'discipline', 'discipline_name', $this->msg['all'])
			);
		$form->addElement('select', 'lang', $this->msg['lang'],
			$this->db->get_row_assoc('SELECT lang, lang_name
				FROM language ORDER BY lang',
				'lang', 'lang_name', $this->msg['all'])
			);
		$form->addElement('select', 'src', $this->msg['ref_source'],
			$this->db->get_row_assoc('SELECT ref_source, ref_source_name
				FROM ref_source WHERE glossary = 1',
				'ref_source', 'ref_source_name', $this->msg['all'])
			);
		$form->addElement('select', 'op', null, $operators);
		$form->addElement('submit', 'srch', $this->msg['search_button']);

		$template = '<span class="search_param" style="white-space:nowrap; margin-right:20px;">%1$s: %2$s</span>' . LF;
		$ret .= $form->begin_form();
		$ret .= '<div class="panel panel-default">' . LF;
		$ret .= '<div class="panel-heading">' . $this->msg['search'] . '</div>' . LF;
		$ret .= '<div class="panel-body">' . LF;
		$ret .= $form->get_element('mod');
		$ret .= sprintf($template, $this->msg['search_op'], $form->get_element('op'));
		$ret .= sprintf($template, $this->msg['phrase'], $form->get_element('phrase'));
		$ret .= sprintf($template, $this->msg['discipline'], $form->get_element('dc'));
		$ret .= sprintf($template, $this->msg['lang'], $form->get_element('lang'));
		$ret .= sprintf($template, $this->msg['ref_source'], $form->get_element('src'));
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
		$query = 'SELECT a.* FROM glossary a
			WHERE a.glo_uid = ' . $this->db->quote($_GET['uid']);
		$this->entry = $this->db->get_row($query);
		$is_new = is_array($this->entry) ? 0 : 1;

		$form = new form('entry_form', null, './' . $this->get_url_param());
		$form->setup($this->msg);
		$form->addElement('text', 'original', $this->msg['en'], array('size' => 40, 'maxlength' => '255'));
		$form->addElement('text', 'phrase', $this->msg['id'], array('size' => 40, 'maxlength' => '255'));
		$form->addElement('select', 'discipline', $this->msg['discipline'], $this->db->get_row_assoc('SELECT * FROM discipline ORDER BY discipline_name', 'discipline', 'discipline_name'));
		$form->addElement('select', 'ref_source', $this->msg['ref_source'], $this->db->get_row_assoc('SELECT * FROM ref_source WHERE glossary = 1', 'ref_source', 'ref_source_name'));
		$form->addElement('text', 'wpen', $this->msg['wpen'], array('size' => 40, 'maxlength' => '255'));
		$form->addElement('text', 'wpid', $this->msg['wpid'], array('size' => 40, 'maxlength' => '255'));
		$form->addElement('hidden', 'glo_uid');
		$form->addElement('hidden', 'is_new', $is_new);
		$form->addElement('submit', 'save', $this->msg['save']);
		$form->addRule('phrase', sprintf($this->msg['required_alert'], $this->msg['id']), 'required', null, 'client');
		$form->addRule('original', sprintf($this->msg['required_alert'], $this->msg['en']), 'required', null, 'client');
		$form->addRule('discipline', sprintf($this->msg['required_alert'], $this->msg['discipline']), 'required', null, 'client');
		$form->addRule('ref_source', sprintf($this->msg['required_alert'], $this->msg['ref_source']), 'required', null, 'client');
		$form->setDefaults($this->entry);

		$ret .= sprintf('<h1>%1$s</h1>' . LF,
			($is_new ? $this->msg['new'] : $this->msg['edit']) .
			' - ' . $this->msg['glossary']
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
		$query = ($is_new ? 'INSERT INTO' : 'UPDATE') . ' glossary SET ';
		$query .= sprintf('
			phrase = %1$s,
			original = %2$s,
			discipline = %3$s,
			ref_source = %4$s,
			wpid = %5$s,
			wpen = %6$s,
			updater = %7$s,
			updated = NOW()',
			$this->db->quote($_POST['phrase']),
			$this->db->quote($_POST['original']),
			$this->db->quote($_POST['discipline']),
			$this->db->quote($_POST['ref_source']),
			$this->db->quote($_POST['wpid']),
			$this->db->quote($_POST['wpen']),
			$this->db->quote($this->auth->getUsername())
			);
		if (!$is_new)
			$query .= sprintf(' WHERE glo_uid = %1$s;',
				$this->db->quote($_POST['glo_uid'])
			);

		// die($query);
		$this->db->exec($query);

		// redirect
		$_GET['phrase'] = $_POST['phrase'];
		redir('./' . $this->get_url_param(array('action', 'uid')));
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
					$uids .= $rows[$uid]['glo_uid'];
				}

				// english wikipedia
				$query = sprintf(
					'UPDATE glossary SET wp%3$s = %1$s WHERE glo_uid IN (%2$s);',
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