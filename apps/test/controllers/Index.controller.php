<?php
class Index_Controller extends Any_Controller
{
	function __actions()
	{
		// 'action mask' => 'method_name:view_name'
		return array(
			'*' => 'index:index',
		);
	}
	
	function index() 
	{
		return array(
			'title' => 'Index_Controller::index'
		);
	}
}
?>