<?php
/**
 * Library of common functions
 */

define(PROCESS_NONE, 0); // mark no process
define(PROCESS_SUCCEED, 1); // mark process succeed
define(PROCESS_FAILED, 2); // mark process failed

/**
 * Redirect to a certain URL page
 *
 * @param $url
 */
function redir($url)
{
    header('Location:' . $url);
}

function login($username = null, $status = null, &$auth = null)
{
    global $msg, $auth, $is_post;
    $welcome = $auth->checkAuth() ? 'login_success' : 'login_welcome';
    $welcome = $msg[$welcome];
    if ($is_post && !$auth->checkAuth()) $welcome = $msg['login_failed'] . ' ' . $welcome;

    $ret .= '<h1>' . $msg['login'] . '</h1>' . LF;
    if (!$auth->checkAuth()) $ret .= sprintf('<p>%1$s</p>' . LF, $msg['login_beta']);
    $ret .= sprintf('<p>%1$s</p>' . LF, $welcome);

    if (!$auth->checkAuth())
    {
        $form = new form('login_form', null, './?mod=user&action=login');
        $form->setup($msg);
        $form->addElement('text', 'username', $msg['username']);
        $form->addElement('password', 'password', $msg['password']);
        $form->addElement('submit', null, $msg['login']);
        $form->addRule('username', sprintf($msg['required_alert'], $msg['username']), 'required', null, 'client');
        $form->addRule('password', sprintf($msg['required_alert'], $msg['password']), 'required', null, 'client');
        $ret .= $form->toHtml();
    }
    return($ret);
}

/**
 * @return Search form HTML
 */
function show_header_old()
{
    global $msg, $auth, $db;
    global $_GET;

    $form = new form('search_form', 'get');
    $form->setup($msg);
    $form->addElement('text', 'phrase', $msg['enter_phrase'],
        array('size' => 15, 'maxlength' => 255));
    $form->addElement('select', 'mod', null, array(
        'dictionary' => $msg['dictionary'],
        'glossary' => $msg['glossary'],
        'proverb' => $msg['proverb'],
    ));
    $form->addElement('submit', 'search', $msg['search_button']);

    $ret .= $form->begin_form();
    $ret .= '<table cellpadding="0" cellspacing="0" width="100%"><tr>' . LF;

    // logo
    $ret .= '<td width="1%">' . LF;
    $ret .= '<a href="./"><img src="images/logo.png" width="32" height="32" border="0" alt="Kateglo" title="Kateglo" /></a>' . LF;
    $ret .= '</td>' . LF;

    // search form
    $template = '<td style="padding-right:2px;">%1$s</td>' . LF;
    $ret .= '<td><table cellpadding="0" cellspacing="0"><tr>' . LF;
    $ret .= sprintf($template, $form->get_element('search'));
    $ret .= sprintf($template, $form->get_element('phrase'));
    $ret .= sprintf($template, $msg['search_in']);
    $ret .= sprintf($template, $form->get_element('mod'));
    $ret .= '</tr></table></td>' . LF;

    // navigation
    $ret .= '<td align="right">' . LF;
    if ($auth->checkAuth())
    {
        $ret .= sprintf('<strong>%3$s</strong> | <a href="%5$s">%4$s</a> | <a href="%2$s">%1$s</a>' . LF,
            $msg['logout'], './?mod=user&action=logout',
            $auth->getUsername(),
            $msg['change_pwd'], './?mod=user&action=password'
        );
    }
    else
        $ret .= sprintf('<a href="%2$s">%1$s</a>' . LF, $msg['login'], './?mod=user&action=login');
    $ret .= '</td>' . LF;

    $ret .= '</tr></table>' . LF;
    $ret .= $form->end_form();

    return($ret);
}

/**
 * @return Search form HTML
 */
function show_header()
{
    global $msg, $auth, $db;
    global $_GET;

    $mods =  array(
        'dictionary' => $msg['dictionary'],
        'glossary' => $msg['glossary'],
        'proverb' => $msg['proverb'],
        'abbr' => $msg['abbr'],
    );
    $navMenu .= sprintf('<a href="./">%1$s</a>', $msg['home']);
    foreach ($mods as $key => $mod)
    {
        $navMenu .='&nbsp;&nbsp;&nbsp;';
        $navMenu .= sprintf('<a href="./?mod=%1$s">%2$s</a>', $key, $mod);
    }

    $form = new form('search_form', 'get');
    $form->setup($msg);
    $form->addElement('text', 'phrase', $msg['enter_phrase'],
        array('size' => 20, 'maxlength' => 255));
    $form->addElement('select', 'mod', null, $mods);
    $form->addElement('submit', 'search', $msg['search_button']);

    $ret .= $form->begin_form();
    // logo
    $ret .= '<div id="header">' . LF;
    $ret .= '<table cellpadding="0" cellspacing="0" width="100%"><tr>' . LF;
    $ret .= '<td width="1%">' . LF;
    $ret .= '<a href="./"><img src="images/kateglo40.png" width="129" height="40" border="0" alt="Kateglo" title="Kateglo" /></a>' . LF;
    $ret .= '</td>' . LF;

    // search form
    $template = '<td style="padding-left:5px;">%1$s</td>' . LF;
    $ret .= '<td align="right"><table cellpadding="0" cellspacing="0"><tr>' . LF;
    $ret .= sprintf($template, $form->get_element('phrase'));
    $ret .= sprintf($template, $msg['search_in']);
    $ret .= sprintf($template, $form->get_element('mod'));
    $ret .= sprintf($template, $form->get_element('search'));
    $ret .= '</tr></table></td>' . LF;
    $ret .= '</tr></table>' . LF;
    $ret .= '</div>' . LF;

    // navigation
    $ret .= '<div id="navbar">' . LF;
    $ret .= '<table cellpadding="0" cellspacing="0" width="100%"><tr>' . LF;
    $ret .= '<td>' . LF;
    $ret .= $navMenu;
    $ret .= '</td>' . LF;
    $ret .= '<td align="right">' . LF;
    if ($auth->checkAuth())
    {
        $ret .= sprintf('%3$s&nbsp;&nbsp;&nbsp;<a href="%5$s">%4$s</a>&nbsp;&nbsp;&nbsp;<a href="%2$s">%1$s</a>' . LF,
            $msg['logout'], './?mod=user&action=logout',
            $auth->getUsername(),
            $msg['change_pwd'], './?mod=user&action=password'
        );
    }
    else
        $ret .= sprintf('<a href="%2$s">%1$s</a>' . LF, $msg['login'], './?mod=user&action=login');
    $ret .= '</td>' . LF;
    $ret .= '</tr></table>' . LF;
    $ret .= '</div>' . LF;

    $ret .= $form->end_form();

    return($ret);
}

/**
 * Footer
 */
function show_footer_old()
{
    global $msg;
    $ret .= sprintf('<div class="container">' .
        '<span style="float:right;">' .
        '<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/">' .
        '<img title="%6$s" alt="%6$s" style="border-width:0" ' .
        'src="./images/cc-by-nc-sa.png" />' .
        '</a></span>' .
        '<a href="%2$s">%3$s</a>' .
        '&nbsp;&#183;&nbsp;' .
        '<a href="%7$s">API</a>' .
        '&nbsp;&#183;&nbsp;' .
        '<a href="%4$s">%5$s</a>' .
        '</div>' . LF,
        APP_SHORT,
        './?mod=doc&doc=README.txt',
        APP_VERSION,
        './?mod=comment',
        $msg['comment_link'],
        'CC-BY-NC-SA',
        './api.php'
    );
    return($ret);
}

/**
 * Footer
 */
function show_footer()
{
    global $msg;
    $ret .= '<div class="footer container">' . LF;
    $ret .= sprintf('<p>' .
        '<span style="float:right;">' .
        '<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/">' .
        '<img title="%6$s" alt="%6$s" style="border-width:0" ' .
        'src="./images/cc-by-nc-sa.png" />' .
        '</a></span>' .
        '<a href="%2$s">%3$s</a>' .
        '&nbsp;&nbsp;&nbsp;' .
        '<a href="%7$s">API</a>' .
        '&nbsp;&nbsp;&nbsp;' .
        '<a href="%4$s">%5$s</a>' .
        '</p>' . LF,
        APP_SHORT,
        './?mod=doc&doc=README.txt',
        APP_VERSION,
        'http://bahtera.org/blog/kateglo/',
        $msg['comment_link'],
        'CC-BY-NC-SA',
        './api.php'
    );
    $ret .= '</div>' . LF;
    return($ret);
}

/**
 * External stat
 */
function get_external_stat()
{

    // gostats
    $ret .= '<!-- GoStats JavaScript Based Code -->';
    $ret .= '<script type="text/javascript" src="http://gostats.com/js/counter.js"></script>';
    $ret .= '<script type="text/javascript">_gos=\'gostats.com\';_goa=728945;_got=5;_goi=1;_goz=0;_gol=\'web traffic software\';_GoStatsRun();</script>';
    $ret .= '<noscript><a target="_blank" title="web traffic software" href="http://gostats.com"><img alt="web traffic software" src="http://gostats.com/bin/count/a_728945/t_5/i_1/counter.png"  style="border-width:0" /></a></noscript>';
    $ret .= '<!-- End GoStats JavaScript Based Code -->' . LF;

    return($ret);
}
?>