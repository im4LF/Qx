<?php
/**
 * # @view "contollerMethod" "template file", if controllerMethod not set - its default template
 * 
 * @view user/default
 * @view ajaxLogin		user/login-form
 * @view simpleLogin	user/simple-login
 * 
 * # @action "request method":"request action"."request view" "class method"
 * 
 * @action *:*.* index
 * 
 * # if "class method" starts with "@" -	its mean that controll will be passed to 
 * #										another controller called action 
 * 
 * @action *:register.* @xUser_Register
 * @action get:login.* loginForm
 * 
 * # [before method1 method2, after method3] mean than "before" and "after" calling controller method 
 * # will be called defined methods - "method1 method2" and "method3"
 * 
 * @action post:login.json ajaxLogin [before ajaxLogin__before, after loginUser]
 */
class xUser extends xAny
{
	function index() {}
	
	/**
	 * # also you may redefine view
	 * @view user/login-form
	 */
	function loginForm() {}
	
	/**
	 * # validation configs: 
	 * #	on - enable validation 
	 * #	auto - automatic validation
	 * #	user - call user-defined validation method "methodName__validate"
	 * # 	
	 * #	also for params "auto" and "user" you may add options - "soft" or "strict"
	 * # 	soft -		validation don't call validation_error 
	 * # 				and pass control to method or user-defined validate method
	 * # 	strict -	validation will call validation_error if errors will be detected
	 * #				and DON'T pass to method or user-defined validation
	 * #    default option is "strict"
	 * @config validation [on, auto]
	 * 
	 * @param tEmail  $email		post [required, valid, exists]
	 * @param tString $password	post [required, min(6) as min]
	 */
	function ajaxLogin($email, $password) {}
	
	function ajaxLogin__before() {}
	
	function ajaxLogin__validation_error(&$email, &$password) {}
	
	/**
	 * @action post:login.* [after clearOldLogin loginUser]
	 * 
	 * @config validation [on, auto soft, user]
	 * @param tEmail  $email 	post [required, valid, exists]
	 * @param tString $password	post [required, min(6) as min]
	 */
	function simpleLogin($email, $password) 
	{
	}
	
	/**
	 * @param array $values 
	 */
	function simpleLogin__validate($errors)
	{
		$errors['email']->value()->update('test@test.test');
		$errors['email']->reset('required')->validate();
		
		return $errors;
	}
	
	function simpleLogin__validation_error($errors)
	{
	}
	
	function clearOldLogin() {}
	
	function loginUser() {}
}
?>