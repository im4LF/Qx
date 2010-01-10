<?php
/**
 * @action get:register.* registerForm
 * @action post:register.json ajaxRegister [after]
 */
class UserRegister_Action extends Any_Controller
{
	function registerForm() {}
	
	/**
	 * @config validation [on, auto]
	 * @param Email  $email				post [required, call(email_unique) as unique]
	 * @param String $password			post [required, min(6) as min]
	 * @param String $confirm_password	post [required, eq($password) as eq]
	 * @param String $firstname			post [required]
	 * @param String $surname			post [required]
	 */
	function ajaxRegister($email, $password, $confirm_password, $firstname, $surname) {}
	
	function email_unique(&$email) 
	{
		return true;
	}
	
	function ajaxRegister__validation_error() {}
	
	function ajaxRegister__after() {}
}
?>