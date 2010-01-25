<?php
/**
 * 
 */
class xUser extends xAny
{
	function __actions()
	{
		return array(
			'*' 				=> 'index:user/default',
			'get:login.*' 		=> 'loginForm:user/login-form',
			'post:login.json' 	=> 'ajaxLogin:user/default',
			'*:register.*' 		=> '@xUser_Register'
		);
	}
	
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