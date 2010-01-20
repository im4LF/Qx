<?php
class Q
{
    static function app($paths)
    {
    	Benchmark::start('app init');
		
        foreach ($paths as $key=>$path)		// define path's constants for application, shared and others
        {
            define(strtoupper($key).'_PATH', realpath($path));
        }
        
		import::scan('app:import.php'); 	// set configuration and scan new paths 
		
		Benchmark::stop('app init');
		
        return QF::n('App');
    }
}
?>
