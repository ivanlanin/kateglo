<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This action performs HTTP redirect to a specific page.
 * 
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    HTML
 * @package     HTML_QuickForm_Controller
 * @author      Alexey Borzov <avb@php.net>
 * @copyright   2003-2007 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id: Jump.php,v 1.5 2007/05/18 09:34:18 avb Exp $
 * @link        http://pear.php.net/package/HTML_QuickForm_Controller
 */

/**
 * Class representing an action to perform on HTTP request.
 */
require_once 'HTML/QuickForm/Action.php';

/**
 * This action performs HTTP redirect to a specific page.
 * 
 * @category    HTML
 * @package     HTML_QuickForm_Controller
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: 1.0.8
 */
class HTML_QuickForm_Action_Jump extends HTML_QuickForm_Action
{
    function perform(&$page, $actionName)
    {
        // check whether the page is valid before trying to go to it
        if ($page->controller->isModal()) {
            // we check whether *all* pages up to current are valid
            // if there is an invalid page we go to it, instead of the
            // requested one
            $pageName = $page->getAttribute('id');
            if (!$page->controller->isValid($pageName)) {
                $pageName = $page->controller->findInvalid();
            }
            $current =& $page->controller->getPage($pageName);

        } else {
            $current =& $page;
        }
        // generate the URL for the page 'display' event and redirect to it
        $action = $current->getAttribute('action');
        $url    = $action . (false === strpos($action, '?')? '?': '&') .
                  $current->getButtonName('display') . '=true' .
                  ((!defined('SID') || '' == SID || ini_get('session.use_only_cookies'))? '': '&' . SID);
        header('Location: ' . $url);
        exit;
    }
}
?>
