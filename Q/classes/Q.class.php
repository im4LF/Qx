<?php
class Q
{
    static function app($paths)
    {
		$appinit_t0 = microtime(true);
		
        foreach ($paths as $key=>$path)				// define path's constants for application, shared and others
        {
            define(strtoupper($key).'_PATH', realpath($path));
        }
        
        $config = import::config('app:import.php');	// load Application import configurations
        QF::s('Configs')->import = $config;			// save configuration in Configs
		import::s()->configure($config)->scan(); 	// set configuration and scan new paths 

		$appinit_t1 = microtime(true);
		echo "appinit dt: ".($appinit_t1 - $appinit_t0)."\n";
		
        return QF::n('App');
    }
}
?>
