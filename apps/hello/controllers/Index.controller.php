<?php
/**
 * @view index
 */
class Index_Controller extends Any_Controller
{
	/**
	 * @action *:*.*
	 */
	function index() 
	{
		// some test requests
		QF::n('Request', '/user.html')->dispatch();
		QF::n('Request', '/user/.login.html', array('method'=>'POST'))->dispatch();
		
		$reg_data = array(
			'email' => 'tester@tester.tester',
			'password' => '123',
			'confirm_password' => '1234'			
		);
		QF::n('Request', '/user/.register.json', array(
			'method' => 'POST',
			'post'	 =>& $reg_data
		))->dispatch();
		
		return array(
			'title' => 'Index_Controller::index'
		);
	}
}
?>