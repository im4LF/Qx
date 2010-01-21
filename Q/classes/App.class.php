<?php
class App 
{
	static function run($paths) 
	{
		Benchmark::start('App::run');
		
        foreach ($paths as $key=>$path)		// define path's constants for application, shared and others
        {
            define(strtoupper($key).'_PATH', realpath($path));
        }
        
		import::configure('app:import.php');	// set new import configuration	
		import::scan(); 						// and scan new paths 
		
		$config = import::config('app:app.php');	// import application configuration
		foreach ($config as $key => $value)			// save all configuration sections in configs		
		{
			F::s('Configs')->$key = $value;
		}
		
		Benchmark::stop('App::run');
		
		F::s('Cache', $config['impls']['cache'])->load();
		
		// create new external request
		F::n('Request', 
				'/',							// raw url for request - $_SERVER['REQUEST_URI']
				array(
					'name'		=> '__main__',	// main request
					'method'	=> 'GET', 		// request method 
					'scenario'	=> 'external',	// scenario name
					'get'		=>& $_GET,
					'post'		=>& $_POST,
					'files'		=>& $_FILES
		))->dispatch();							// run request dispatching
		
		F::s('Cache')->save();
		
		echo print_r(Benchmark::get(), 1);
	}
	
}
?>