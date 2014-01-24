<?php
/**
 * Common form function wrapper
 *
 * @author ivan@lanin.org
 */
require_once('HTML/QuickForm.php');

class form extends HTML_QuickForm
{
	var $msg;

    /**
     * @return unknown_type
     */
    function setup($msg)
    {
		$this->msg = $msg;
    	$this->setJsWarnings($msg['form_err_pre'], $msg['form_err_post']);
    	$this->setRequiredNote($msg['form_required']);
    }

	/**
	 * @param $element
	 * @return HTML code of the element
	 */
	function get_element($element)
	{
		return(preg_replace('/^\t+/m', '', $this->getElement($element)->toHtml()));
	}

	/**
	 * @return unknown_type
	 */
	function begin_form()
	{
		$form_array = $this->toArray();
		return('<form' . $form_array['attributes'] . '>' . LF);
	}

	/**
	 * @return unknown_type
	 */
	function end_form()
	{
		$form_array = $this->toArray();
		return($form_array['javascript']. LF . '</form>' . LF);
	}

	/**
	 * Strip tabs from original toHtml
	 */
	function toHtml()
	{
		return(preg_replace('/^\t+/m', '', parent::toHtml()));
	}
}
?>