<?php
/**
 * @view index
 */
class xIndex extends xAny
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