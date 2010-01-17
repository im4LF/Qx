<?php
class App 
{
	public function run() 
	{
		$config = import::config('app:app.php');	// import application configuration
		foreach ($config as $key => $value)			// save all configuration sections in configs		
		{
			QF::s('Configs')->$key = $value;
		}
		
		// create new external request
		QF::n('Request', 
				'/',							// raw url for request - $_SERVER['REQUEST_URI']
				array(
					'name'		=> '__main__',	// main request
					'method'	=> 'GET', 		// request method 
					'scenario'	=> 'external',	// scenario name
					'get'		=>& $_GET,
					'post'		=>& $_POST,
					'files'		=>& $_FILES
		))->dispatch();							// run request dispatching
	}
	
}
?>