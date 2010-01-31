<?php
class Index_Controller extends Any_Controller
{
	// $__action_[action name]_[method]_[viewtype] = 'method_name:view_name'
	// action name - action name
	// method      - request method - GET or POST in lowercase
	// viewtype    - request viewtype
	// method_name - controller method
	// view_name   - template name
	
	// $__action_x - mean any action any method and any viewtype - call if not matched any action
	
	// $__action_[action name]_[method]_[viewtype] = 'method_name:' - defined just method name, 
	// view name gets from $__action_x, also ':method_name' mean that method name well gets of $__action_x
	public $__x = 'index:index';
	
	function index() 
	{
		return array(
			'title' => 'Index_Controller::index'
		);
	}
}
?>