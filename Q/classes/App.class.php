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
		
		$caches_configs = import::config('app:caches.php');	// caches configuration
		F::s('Configs')->caches = $caches_configs;			// save configurations
		foreach ($caches_configs as $key => $config)		// initialize all autoload caches
		{
			if (!$config['autoload'])	// if cache not autoload
				continue;
				
			echo "Cache:$key\n";
			F::r('Cache:'.$key, $config)->load();
		}
		
		F::s('Configs')->impls = import::config('app:impls.php');			// impls configuration
		F::s('Configs')->scenarios = import::config('app:scenarios.php');	// scenarios configuration
		
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
		
		foreach ($caches_configs as $key => $config)	// save all autosave caches
		{
			if (!$config['autosave'])	// if cache not autosave
				continue;
				
			F::r('Cache:'.$key)->save();		
		}
		
		echo print_r(Benchmark::get(), 1);
	}
	
}
?>