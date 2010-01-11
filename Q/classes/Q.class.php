<?php
class Q
{
    static function app($paths)
    {
    	QF::s('Benchmark')->start('app init');
		
        foreach ($paths as $key=>$path)				// define path's constants for application, shared and others
        {
            define(strtoupper($key).'_PATH', realpath($path));
        }
        
        $config = import::config('app:import.php');	// load Application import configurations
        QF::s('Configs')->import = $config;			// save configuration in Configs

		import::s()->configure($config)->scan('app:cache/import.txt'); 	// set configuration and scan new paths 
		
		echo 'dt imports:'.print_r(import::s()->stats(), 1);
		echo 'dt app init: '.QF::s('Benchmark')->stop('app init')."\n";
		
        return QF::n('App');
    }
}
?>
