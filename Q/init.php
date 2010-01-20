<?php 
define('Q_PATH', realpath(dirname(__FILE__)));

import::configure('q:import.php');
import::register();
import::from('q:utils/Benchmark');

$key = 'start';
uBenchmark::start($key);

new uDocCommentParser;
new uF;

uBenchmark::stop($key);
print_r(uBenchmark::get());

class import
{
	private static $_config;
	private static $_data;
	private static $_ext_mask;
	
	static function configure($config_file)
	{
		self::$_config = self::config($config_file);
		self::$_ext_mask = '/'.str_replace('.', '\.', self::$_config['ext']).'$/';
	}
	
	static function register()
	{
		spl_autoload_register(array('import', 'importClass'));
	}
	
	static function unregister()
	{
		spl_autoload_unregister(array('import', 'importClass'));
	}
	
	static function config($path)
	{
		$spos = strpos($path, ':');
		if (false === ($path = self::buildPath(substr($path, 0, $spos).':configs/'.substr($path, 1+$spos)))
			|| !file_exists($path))
			return;
			
		return require($path);
	}
	
	static function from($mask)
	{
		if (false === strpos($mask, '/'))
            import::importClass($mask);
        else
            import::importFiles($mask);
	}
	
	static function importClass($class_name)
    {
    	$key = "load [$class_name]";
    	uBenchmark::start($key);
    	$m = array();
		if (!preg_match('/^([a-z])[A-Z]/', $class_name, $m) || !array_key_exists($m[1], self::$_config['scanner']))
			return false;
		
		$class_file = str_replace('_', DIRECTORY_SEPARATOR, substr($class_name, 1)).self::$_config['ext'];
		$paths = self::$_config['scanner'][$m[1]];
		if (!is_array($paths) && false !== ($paths = self::buildPath($paths)))
		{
			$filename = $paths.DIRECTORY_SEPARATOR.$class_file;
			require $filename;
			uBenchmark::stop($key);
			return true;
		}
		else
		{
			$n = count($paths);
			for ($i = 0; $i < $n; $i++)
			{
				if (false === ($path = self::buildPath($paths[$i])))
					continue;
					
				$filename = $path.DIRECTORY_SEPARATOR.$class_file;
				
				if (!file_exists($filename))
					continue;

				require $filename;
				uBenchmark::stop($key);
				return true;
			}
		}
		uBenchmark::stop($key);
		return false;
	}
	
	static function importFiles($path)
    {
    	if (false === ($path = self::buildPath($path)))
        	throw new Exception("type of path [$path] not defined");
			
        if (strpos($path, '*'))	// import directory recursively
        {
        	foreach (glob($path) as $item)
			{
				if (!is_dir($item))	// its simple file
				{
					self::_importFile($item);
					continue;
				}
				
				$di = new RecursiveDirectoryIterator($item);
				foreach (new RecursiveIteratorIterator($di) as $fileinfo)
					self::_importFile($fileinfo->getPathname());
			}
        }
        else	// import simple file
            self::_importFile($path.self::$_config['ext']);
    }
	
	static function buildPath($path)
	{
		list($type, $path) = explode(':', $path);
		$type_const = strtoupper($type).'_PATH';
		
		if (!defined($type_const)) 
			return false;
			
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        return constant($type_const).DIRECTORY_SEPARATOR.$path;
	}
	
    private static function _importFile($path)
    {
    	if (!preg_match(self::$_ext_mask, $path)) 
			return;

		require($path);		
    }
}
?>