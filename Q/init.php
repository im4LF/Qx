<?php 
define('Q_PATH', realpath(dirname(__FILE__)));
//define('APP_PATH', realpath('../apps/hello'));

import::configure('q:import.php');						// configure import
import::from('q:classes/utils/Benchmark.class.php');	// import Q_PATH/utils/Benchmark.class.EXT
import::from('q:classes/utils/F.class.php');			// import Q_PATH/utils/F.class.EXT - factory class
import::scan();											// scan defined paths for classes
import::register(); 									// register autoloader

/*$test = array(
	'views' => array(
		'*' => 'index'
	)
);
$test2 = array(
	'views' => array(
		'*' => 'user/index'
	)
);

$config = import::config('app:cache.php');

F::r('Cache:controllers', $config['controllers'])->set('Index', $test);
F::r('Cache:controllers', $config['controllers'])->set('views:Index', 'index');

F::r('Cache:controllers2', $config['controllers'])->set('User', $test2);

print_r(F::r('Cache:controllers')->get('views:Index'));
print_r(F::r('Cache:controllers2'));*/

class import
{
	private static $_config_file;
	private static $_config;
	private static $_data;
	private static $_counter;
	
	static function configure($config_file)
	{
		self::$_config_file = $config_file;
		self::$_config = self::config($config_file);
	}
	
	static function register()
	{
		spl_autoload_register(array('import', 'importClass'));
	}
 
	static function unregister()
	{
		spl_autoload_unregister(array('import', 'importClass'));
	}
	
	static function scan()
	{
		$b_key = 'import::scan ['.self::$_config_file.']';
		Benchmark::start($b_key);
		
		if (false !== ($cache = self::_cache())) 
		{
			self::$_data = $cache;
			Benchmark::stop($b_key);
			echo 'founded: '.print_r(self::$_data, 1);
			return;
		}
		
		$n = count(self::$_config['scanner']['directories']);
		for ($i = 0; $i < $n; $i++)
		{
			if (false === ($path = self::buildPath(self::$_config['scanner']['directories'][$i])))
				continue;
			
			self::_scanDirectory($path);
		}
		
		self::_cache(self::$_data);
		
		Benchmark::stop($b_key);
	}
	
	private static function _cache($value = null)
	{
		if (!self::$_config['cache']['enabled'])
			return false;

		$cache_file = self::$_config['cache']['file'];
		
		if (false === ($cache_file = self::buildPath($cache_file)))
			return false;
		
		if (!$value && !file_exists($cache_file))	// value not set - its mean load from cache 
			return false;
			
		if ($value) // save to cache
		{
			file_put_contents($cache_file, serialize($value));
			return true;
		}
		
		return unserialize(file_get_contents($cache_file));
	}
	
	private static function _scanDirectory($path)
	{
		if (!file_exists($path)) 
			return;
		
		$di = new RecursiveDirectoryIterator($path);
		foreach (new RecursiveIteratorIterator($di) as $fileinfo)
		{
			$filename = $fileinfo->getPathname();
			
			if (!preg_match(self::$_config['scanner']['filenames'], $filename)) 
				continue;
				
			$content = file_get_contents($filename);
            if (!preg_match_all('/^\s*class\s+(\w+)/im', $content, $matches, PREG_SET_ORDER)) 
				continue;
               
			self::$_data['files'][$filename] = @self::$_data['files'][$filename];
			    
            foreach ($matches as $match)
            {
				self::$_data['classes'][$match[1]] = array(
					'name' => $match[1],
					'path' => $filename,
					'loaded' =>& self::$_data['files'][$filename]
				);
            }
		}			
	}
	
	static function from($mask)
	{
		if (false === strpos($mask, '/'))
            import::importClass($mask);
        else
            import::importFiles($mask);
	}
	
	static function config($path)
	{
		list($type, $path) = explode(':', $path);
		if (false === ($path = self::buildPath($type.':configs/'.$path)) || !file_exists($path))
			return;
		
		return require($path);
	}
	
	static function importClass($class_name)
    {
    	$b_key = 'import::importClass #'.++self::$_counter.' ['.$class_name.']';
		Benchmark::start($b_key);
		
		if (!array_key_exists($class_name, self::$_data['classes']))	// class not found
			return false;

		if (self::$_data['classes'][$class_name]['loaded'])	// class already loaded 
			return; 
 
		require(self::$_data['classes'][$class_name]['path']);	// load file
		self::$_data['classes'][$class_name]['loaded'] = true;
		
		Benchmark::stop($b_key);
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
            self::_importFile($path);
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
    	if (!preg_match(self::$_config['import']['mask'], $path) || isset(self::$_data['files'][$path])) 
			return;

		require($path);		
		self::$_data['files'][$path] = true;
    }
}
?>