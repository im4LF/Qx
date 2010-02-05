<?php
class Index_Controller extends Any_Controller
{
	// $__[action name]__[method]_[viewtype] = 'method: [method name]; view: [view name]'
	// action name - action name
	// method      - request method - GET or POST in lowercase
	// viewtype    - request viewtype
	// method name - controller method
	// view name   - template name
	
	// $__x - mean any action any method and any viewtype - call if not matched any action
	
	// define just method name, mean that view name gets from $__x, 
	// also define just view name mean that method name well gets from $__x
	public $__x = 'method: index; view: index; validation: on strict';
	public $__test__x = 'method: testMethod';
	
	function index() 
	{
		return array(
			'title' => 'Index_Controller::index'
		);
	}
	
	function testMethod() 
	{
		return array(
			'title' => 'Index_Controller::testMethod'
		);
	}
}
?>