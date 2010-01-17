<?php
/**
 * @view index
 */
class Index_Controller extends Any_Controller
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