<?php
$__r = array();	// registry holder
$__s = array(); // sigleton holder

import::init();
import::scan('app:configs/import.php');
import::register();

/**
 * Class factory
 * 
 * @example F('SomeClass', $param1, ...)->objectMethod(); - create new instance of SomeClass
 * 
 * @return object
 */
function F()
{
	$args = func_get_args();
    $class_name = array_shift($args);
    
    $key = "new [$class_name]";
    Benchmark::start($key);
    
    $object = null;
    switch (count($args))
    {
        case 0:
            $object = new $class_name(); break;
        case 1:
            $object = new $class_name($args[0]); break;
        case 2:
            $object = new $class_name($args[0], $args[1]); break;
        case 3:
            $object = new $class_name($args[0], $args[1], $args[2]); break;
        case 4:
            $object = new $class_name($args[0], $args[1], $args[2], $args[3]); break;
        case 5:
            $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4]); break;
        case 6:
            $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]); break;
        case 7:
            $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]); break;
    }
	
    Benchmark::stop($key);
    return $object;
}

/**
 * Singleton emulation
 * 
 * @example S('SomeClass', $param1, $param2)->objectMethod(); - create new instance of class SomeClass
 * @example S('SomeClass')->objectMethod(); - return previously created object
 * 
 * @return object 
 */
function S()
{
	global $__s;
	
	$args = func_get_args();
    $class_name = $args[0];
	
    if (!array_key_exists($class_name, $__s))
        $__s[$class_name] = call_user_func_array('F', $args);

    return $__s[$class_name];
}

/**
 * Registry emulation
 * 
 * @example R('my-key', new SomeObject)->objectMethod();
 * @example R('my-key')->objectMethod();
 * 
 * @param string $key   registry key for value
 * @param mixed  $value [optional] value for key
 * @return mixed        if not defined - current value, previously defined if key already exists 
 */
function R($key, $value = null)
{
	global $__r;
	
	if (!array_key_exists($key, $__r))
        $__r[$key] = $value;

    return $__r[$key];
}

/**
 * import helper
 */
class import
{
	private static $_config;
	private static $_data;
	private static $_counter;
	
	static function init()
	{
		self::$_data['configs'] = array();
	}
	
	static function register()
	{
		spl_autoload_register(array('import', 'importClass'));
	}
 
	static function unregister()
	{
		spl_autoload_unregister(array('import', 'importClass'));
	}
	
	static function scan($config_file)
	{
		$b_key = 'import::scan ['.$config_file.']';
		Benchmark::start($b_key);
		
		self::$_config = self::config($config_file);
		
		if (false !== ($cache = self::_cache())) 
		{
			self::$_data = $cache;
			Benchmark::stop($b_key);
			return;
		}
		
		$n = count(self::$_config->scanner['directories']);
		for ($i = 0; $i < $n; $i++)
		{
			if (false === ($path = self::buildPath(self::$_config->scanner['directories'][$i])))
				continue;
			
			self::_scanDirectory($path);
		}
		
		self::_cache(self::$_data);
		Benchmark::stop($b_key);
	}
	
	private static function _cache($value = null)
	{
		if (!self::$_config->cache['enabled'])
			return false;

		$cache_file = self::$_config->cache['file'];
		
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
			
			if (!preg_match(self::$_config->scanner['filenames'], $filename)) 
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
	
	/**
	 * Import class by name or files by mask
	 * 
	 * @example import::from('app:classes/utils/Some.class.php'); - load file
	 * @example import::from('app:classes/utils/*'); - load all files
	 * @example import::from('Request'); - load Request class
	 * 
	 * @param string $mask
	 * @return 
	 */
	static function from($mask)
	{
		return false === strpos($mask, '/') ? import::importClass($mask) : import::importFiles($mask);   
	}
	
	/**
	 * Import configuration
	 * 
	 * @example import::config('app:path/to/config.file'); - load from APP_PATH/configs/path/to/config.file
	 * 
	 * @param string $path    configuration file path
	 * @return object         first level keys of configuration array converted in object 
	 */
	static function config($file)
	{
		if (array_key_exists($file, self::$_data['configs']))
			return self::$_data['configs'][$file];
			
		$key = $file;
			
		if (false === ($file = self::buildPath($file)) || !file_exists($file))
			return false;
		
		self::$_data['configs'][$key] = (object) require($file);
		return self::$_data['configs'][$key];
	}
	
	/**
	 * Import class by name
	 * 
	 * @param string $class_name
	 * @return string filename of class
	 */
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
		return self::$_data['classes'][$class_name]['path'];
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
    	if (!preg_match(self::$_config->import['mask'], $path) || isset(self::$_data['files'][$path])) 
			return;

		require($path);		
		self::$_data['files'][$path] = true;
    }
}

class Benchmark
{
	static $marks;
	
	static function start($key)
	{
		self::$marks[$key]->t0 = microtime(true);
	}
	
	static function stop($key)
	{
		self::$marks[$key]->t1 = microtime(true);
		self::$marks[$key]->time = self::$marks[$key]->t1 - self::$marks[$key]->t0;
		self::$marks[$key]->memory = self::memory();
		
		return self::$marks[$key];
	}
	
	static function get($key = null)
	{
		if (!$key)
			return self::$marks;
		
		return self::$marks[$key];
	}
	
	static function memory()
	{
		static $func;

		if (null === $func)
			$func = function_exists('memory_get_usage');

		return $func ? memory_get_usage() : 0;
	}
}

/**
 * Request object
 */
class Request
{
    protected $_raw_url;
	protected $_url;
	protected $_scenario;
    protected $_alias;
    protected $_method;
    protected $_cookie;   
    protected $_get;
    protected $_post;
    protected $_files;
   
    function __construct($raw_url = '')
    {
        return $this->rawURL($raw_url);
    }
   
    function __call($name, $args = array())
    {
        $property = '_'.strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
       
        if (!count($args))
            return $this->$property;

        if ('_cookie' === $property || '_get' === $property || '_post' === $property || '_files' === $property)
        {
            if (is_string($args[0]))
            {
                if (!isset($args[1]))
                    return $this->{$property}[$args[0]];
                   
                $this->{$property}[$args[0]] = $args[1];
                return $this;
            }
        }

        $this->$property = $args[0];
        return $this;
    }
   
    function dispatch()
    {
    	$scenario_config = import::config('app:configs/app.php')->scenarios[$this->_scenario];
		$scenario_class = $scenario_config['class'];
		$scenario_impls = $scenario_config['impls'];
		$this->_url = F($scenario_impls['url'])->parse($this->raw_url);
		
        return $this;
    }
}

/**
 * Create new Request object
 * 
 * @param string $raw_url [optional] - raw url for Request
 * @return 
 */
function Request($raw_url = '')
{
    return new Request($raw_url);
}
?>