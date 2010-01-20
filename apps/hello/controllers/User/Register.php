<?php
/**
 * @view user/register
 * @action get:register.* getRegistrationFields
 * @action post:register.json ajaxRegister [after]
 */
class xUser_Register extends xAny
{
	function getRegistrationFields() {}
	
	/**
	 * @config validation [on, auto]
	 * @param tEmail  $email			post [required, call(email_unique) as unique]
	 * @param tString $password			post [required, min(6) as min]
	 * @param tString $confirm_password	post [required, eq($password) as eq]
	 * @param tString $firstname		post [required]
	 * @param tString $surname			post [required]
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