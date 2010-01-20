<?php
class App 
{
	static function run($paths) 
	{
		Benchmark::start('app init');
		
        foreach ($paths as $key=>$path)		// define path's constants for application, shared and others
        {
            define(strtoupper($key).'_PATH', realpath($path));
        }
        
		import::scan('app:import.php'); 	// set configuration and scan new paths 
		
		$config = import::config('app:app.php');	// import application configuration
		foreach ($config as $key => $value)			// save all configuration sections in configs		
		{
			QF::s('Configs')->$key = $value;
		}
		
		Benchmark::stop('app init');
		
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