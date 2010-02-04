<?php
/**
 * 
 */
class User_Register_Action extends Any_Controller
{
	public $__register__get_x = 'method: getRegistrationFields; view: user/register';
	// also in action configuration u may defined other params
	// validation: on strict - mean enable validatoin and switch to strict mode
	// strict validation mode mean call __validation_error if validation not success
	public $__register__post_x = 'method: ajaxRegister; validation: on strict';
	
	// soft validation mode mean call controller method and pass
	// public $__register__post_x = 'method: ajaxRegister; validation: on soft; pass-args: as-array';
	
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