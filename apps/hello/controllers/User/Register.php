<?php
/**
 * @view user/register
 * @action get:register.* getRegistrationFields
 * @action post:register.json ajaxRegister [after]
 */
class cUser_Register extends cAny
{
	function getRegistrationFields() {}
	
	/**
	 * @config validation [on, auto]
	 * @param TEmail  $email				post [required, call(email_unique) as unique]
	 * @param TString $password			post [required, min(6) as min]
	 * @param TString $confirm_password	post [required, eq($password) as eq]
	 * @param TString $firstname			post [required]
	 * @param TString $surname			post [required]
	 * 
	 * @param Email_Type  $email				post [required, call(email_unique) as unique]
	 * @param String_Type $password			post [required, min(6) as min]
	 * @param String_Type $confirm_password	post [required, eq($password) as eq]
	 * @param String_Type $firstname			post [required]
	 * @param String_Type $surname			post [required]
	 * 
	 * @param tEmail  $email				post [required, call(email_unique) as unique]
	 * @param tString $password			post [required, min(6) as min]
	 * @param tString $confirm_password	post [required, eq($password) as eq]
	 * @param tString $firstname			post [required]
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