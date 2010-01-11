<?php
class App 
{
	public function run() 
	{
		$config = import::config('app:app.php');			// import application configuration
		QF::s('Configs')->impls	= $config['impls'];			// save implementations
		QF::s('Configs')->scenarios	= $config['scenarios'];	// save scenarios
		
		// create new external request
		QF::n('Request', 
				'/',							// raw url of request 
				array(
					'method'	=> 'GET', 		// request method 
					'scenario'	=> 'external',	// scenario name
					'get'		=>& $_GET,
					'post'		=>& $_POST,
					'files'		=>& $_FILES
		))->dispatch();							// run request dispatching
				
		//QF::n('Request', '/user.html')->dispatch();
		//QF::n('Request', '/user/.login.html', array('method'=>'POST'))->dispatch();
		
		/*$reg_data = array(
			'email' => 'tester@tester.tester',
			'password' => '123',
			'confirm_password' => '1234'			
		);
		QF::n('Request', '/user/.register.json', array(
			'method' => 'POST',
			'post'	 =>& $reg_data
		))->dispatch();*/
		
		//echo print_r(import::s(), 1);
		//echo print_r(QF::s('Configs'), 1);
		//echo print_r(QF::s('RequestManager'), 1);
	}
	
}
?>