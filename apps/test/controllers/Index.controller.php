<?php
class Index_Controller extends Any_Controller
{
	// $__[action name]__[method]_[viewtype] = '[method name]:[view name]'
	// action name - action name
	// method      - request method - GET or POST in lowercase
	// viewtype    - request viewtype
	// method name - controller method
	// view name   - template name
	
	// $__x - mean any action any method and any viewtype - call if not matched any action
	
	// define just method name, mean that view name gets from $__x, 
	// also define just view name mean that method name well gets from $__x
	public $__x = ':index';
	public $__test__x = 'testMethod:index';
	
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
		
		return array(
			'title' => 'Index_Controller::testMethod'
		);
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