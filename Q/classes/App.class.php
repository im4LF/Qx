<?php
class App 
{
	public function run() 
	{
		Benchmark::start('app run');
		
		$config = import::config('app:app.php');	// import application configuration
		foreach ($config as $key => $value)			// save all configuration sections in configs		
		{
			QF::s('Configs')->$key = $value;
		}
		
		// create new external request
		QF::n('Request', 
				'/',							// raw url for request - $_SERVER['REQUEST_URI']
				array(
					'method'	=> 'GET', 		// request method 
					'scenario'	=> 'external',	// scenario name
					'get'		=>& $_GET,
					'post'		=>& $_POST,
					'files'		=>& $_FILES
		))->dispatch();							// run request dispatching
		
		echo 'dt app run: '.Benchmark::stop('app run')->time."\n";
		
		//$memory = function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0;

		//echo 'memory: '. number_format($memory, 2)."MB\n";
		//echo 'included_files: '.count(get_included_files());
		
		//echo print_r(import::s(), 1);
		//echo print_r(QF::s('Configs'), 1);
		//echo print_r(QF::s('RequestManager'), 1);
	}
	
}
?>