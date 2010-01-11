<?php
class App 
{
	public function run() 
	{
		QF::s('Benchmark')->start('app');
		
		$config = import::config('app:app.php');	// import application configuration
		QF::s('Configs')->impls	= $config['impls'];					// save implementations
		QF::s('Configs')->scenarios	= $config['scenarios'];			// save scenarios
		
		QF::s('Benchmark')->start('request: /');
		
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
				
		echo 'request /: '.QF::s('Benchmark')->stop('app')."\n";
				
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
		
		echo 'dt app: '.QF::s('Benchmark')->stop('app')."\n";
		
		$memory = function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0;

		echo 'memory:'. number_format($memory, 2)."MB\n";
		echo 'included_files:'.count(get_included_files());
		//echo print_r(import::s(), 1);
		//echo print_r(QF::s('Configs'), 1);
		//echo print_r(QF::s('RequestManager'), 1);
	}
	
}
?>