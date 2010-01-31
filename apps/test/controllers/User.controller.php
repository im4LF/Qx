<?php
/**
 * 
 */
class User_Controller extends Any_Controller
{
	public $__x				= 'index:user/default';
	public $__login__get_x	= 'loginForm:user/login-form';
	public $__login__post_x	= 'ajaxLogin:user/default';
	
	// $__action_[action_name]_[method]_[viewtype] = '@Some_Other_Controller';
	// it's mean that [Some_Other_Controller] will handle all relevant actions
	// in this case all [register] actions will be handled by [User_Register_Action]
	public $__register__x	= '@User_Register_Action';
	
	function index() 
	{
		return array(
			'title' => 'User_Controller::index'
		);
	}
	
	function loginForm() {}
	
	function ajaxLogin($email, $password) {}
	
	function ajaxLogin__validate() 
	{
		$post = $this->_request->post;
		$values = array();
		
		$values['email'] = Validator::init($post['email'], 'Email')
			->required()->valid()->exist()->validate();
			
		$values['password'] = Validator::init($post['email'], 'String')
			->required()->min(6)->validate();
			
		return $values;
	}
	
	function ajaxLogin__validation_error($errors) {}
	
	/*function get_Login() {}

	function post_Login_json($email, $password) {}
	
	function post_Login_json__validate($cookie = null, $args, $get, $post, $files)
	{
		$post = $this->_request->post;
		
		$values = array();
		$values['email'] = Validator::init($post['email'], 'Email')
			->required()->valid()->exist()->validate();
			
		$values['password'] = Validator::init($post['email'], 'String')
			->required()->min(6)->validate();
			
		return $values;
	}
	
	function post_Login_json__validation_error($errors) {}
	
	function post_Login($email, $password) {}
	
	function post_Login__validate()
	{
		$post = $this->_request->post;
		$values = array();
		
		$values['email'] = Validator::init($post['email'], 'Email')
			->required()->valid()->validate();
			
		$values['password'] = Validator::init($post['password'], 'Password')
			->required()->min(6)->validate();
		
		//$values['email']->value()->update('test@test.test');
		//$values['email']->reset('required')->validate();
		
		return $values;
	}
	
	function post_Login__validation_error($errors) {}
	
	function post_Login__after() {}*/
}
?>