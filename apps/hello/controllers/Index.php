<?php
/**
 * @view index
 */
class cIndex extends cAny
{
	/**
	 * @action *:*.*
	 */
	function index() 
	{
		return array(
			'title' => 'Index_Controller::index'
		);
	}
}
?>