<?php
class App 
{
	static function run($paths) 
	{
		Benchmark::start('App::init');
		
        foreach ($paths as $key=>$path)		// define path's constants for application, shared and others
        {
            define(strtoupper($key).'_PATH', realpath($path));
        }
        
		import::configure('app:import.php');	// set new import configuration	
		import::scan(); 						// and scan new paths 
		
		$configs = import::config('app:app.php');		// load all app configuration sections
		foreach ($configs as $key => $value)
		{
			F::s('Configs')->$key = $value;				// save each configuration ыусешщт
		}
		
		foreach ($configs['caches'] as $key => $config)	// initialize all autoload caches
		{
			if (!$config['autoload'])	// if cache not autoload
				continue;
				
			F::r('Cache:'.$key, $config)->load();
		}
		
		Benchmark::stop('App::init');
		
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
		
		/*foreach ($caches_configs as $key => $config)	// save all autosave caches
		{
			if (!$config['autosave'])	// if cache not autosave
				continue;
				
			F::r('Cache:'.$key)->save();		
		}*/
		
		echo print_r(Benchmark::get(), 1);
	}
	
}
?>