<?php
class Index_Controller extends Any_Controller
{
	// $__[action name]__[http method]_[viewtype] = 'm:[method name], v:[view name], x:[on/off], xm:[strict/soft], a:[params/array], <:[on/off], >:[on/off]'
	// action name	- action name
	// http method	- request method - GET or POST in lowercase
	// viewtype		- request viewtype
	// m 			- controller method
	// v			- template name
	// x			- validation, on/off
	// xm			- validation mode, strict - call __validation_error method, soft - call controller method anyway
	// a			- pass arguments, params/array, params - mean pass each argument as method argument, array - pass all arguments as on array
	
	// $__x - mean any action any method and any viewtype - call if not matched any action
	
	// define just method name, mean that view name gets from $__x, 
	// also define just view name mean that method name well gets from $__x
	// also other keys are inherited
	public $__x = 'v:index';
	public $__test__x = 'm:testMethod, v:index, x:on, xm:soft, a:params, >:on';
	
	function index() 
	{
		return array(
			'title' => 'Index_Controller::index'
		);
	}
	
	function testMethod($email, $password) 
	{
		$args = func_get_args();
		echo 'args: '.print_r($args, 1);
		echo 'email: '.print_r($email, 1);
		
		return array(
			'title' => 'Index_Controller::testMethod'
		);
	}
	
	function testMethod__after($values)
	{
		echo 'testMethod__after: '.print_r($values, 1);
	}
	
	function testMethod__validate()
	{
		$email = new Email('test@test.ru');
		$password = new String('123456');
		return array(
			'email' => $email->correct(),
			'password' => $password->min(6)
		);
	}
	
	function testMethod__validation_error($errors)
	{
		echo 'errors: '.print_r($errors, 1)."\n";
		return array(
			'title' => 'Index_Controller::testMethod__validation_error'
		);
	}
}
?>