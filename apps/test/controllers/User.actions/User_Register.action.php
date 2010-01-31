<?php
/**
 * 
 */
class User_Register_Action extends Any_Controller
{
	public $__register__get_x = 'getRegistrationFields:user/register';
	public $__register__post_x = 'ajaxRegister:';
	
	function getRegistrationFields() {}
	
	function ajaxRegister($email, $password, $confirm_password, $firstname, $surname) {}
	
	function check_email_unique(&$email) 
	{
		return true;
	}
	
	function ajaxRegister__validate()
	{
		$post = $this->_request->post;
		$values = array();
		
		$values['email'] = Validator::init(@$post['email'], 'Email')
			->callbacks($this)
			->required()->valid()->call('check_email_unique')->_as('unique')->exists()->validate();
			
		$values['password'] = Validator::init(@$post['password'], 'String')
			->required()->min(6)->validate();
			
		$values['confirm_password'] = Validator::init(@$post['confirm_password'], 'String')
			->eq($values['password']->value())->validate();
			
		$values['surname'] = Validator::init(@$post['surname'], 'String')
			->optional()->validate();
			
		$values['firstname'] = Validator::init(@$post['firstname'], 'String')
			->optional()->validate();
			
		return $values;
	}
	
	function ajaxRegister__validation_error($errors) 
	{
		return $errors;
	}
	
	function ajaxRegister__after() {}
}
?>