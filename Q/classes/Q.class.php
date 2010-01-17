<?php
class Q
{
    static function app($paths)
    {
    	Benchmark::start('app init');
		
        foreach ($paths as $key=>$path)				// define path's constants for application, shared and others
        {
            define(strtoupper($key).'_PATH', realpath($path));
        }
        
        $config = import::config('app:import.php');	// load Application import configurations
        QF::s('Configs')->import = $config;			// save configuration in Configs

		import::s()->configure($config); 			// set configuration and scan new paths 
		
		//echo 'dt imports:'.print_r(import::s()->stats(), 1);
		echo 'dt app init: '.Benchmark::stop('app init')->time."\n";
		
        return QF::n('App');
    }
}
?>
