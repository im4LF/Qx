<?php
$__r = array();	// registry holder
$__s = array(); // sigleton holder

import::init();
import::register();

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
	static function config($path)
	{
		$key = $path;
		if (array_key_exists($key, self::$_data['configs']))
			return self::$_data['configs'][$key];
			
		if (false === ($path = self::buildPath($path)) || !file_exists($path))
			return false;
		
		self::$_data['configs'][$key] = (object) require($path);
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
 * Singleton amulation
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

function Request($url, $params)
{
	return new Request($url, $params);
}

class Request 
{
	public $raw_url;
	public $url;
	public $method = 'GET';
	public $scenario = 'internal';
	public $cookie;
	public $get;
	public $post;
	public $files;
	
	function __construct($url, $params = array()) 
	{
		$this->raw_url = $url;
		foreach ($params as $name => $value)
			$this->$name = $value;
		
		$this->method = strtolower($this->method);
	}
	
	function dispatch() 
	{
		$scenario_config = import::config('app:configs/app.php')->scenarios[$this->scenario];
		$scenario_class = $scenario_config['class'];
		$scenario_impls = $scenario_config['impls'];
		
		$this->url = F($scenario_impls['url'])->parse($this->raw_url);
		
		F($scenario_class, $this, $scenario_impls)	// create scenario
			->open()			// init scenario
			->run()				// run
			->close();			// close
	}
}

class Runner
{
	public $controller;
	public $result;
	
	function __construct(&$controller)
	{
		$this->controller =& $controller;
	}
	
	function run($method_name)
	{
		$validation_method = $method_name.'__validate';
		$validation_result = array();
		
		if (method_exists($this->controller, $validation_method))	// if validation enabled
		{
			echo "run $validation_method\n";
			$validation_success = true;
			$validation_result = (array) $this->controller->$validation_method();	// run validation method
			$config = array();
			foreach ($validation_result as $name => $result)	// check all values on error
			{
				if ('__config__' === $name)
				{
					$config = $this->_parseConfig($result);
					unset($validation_result[$name]);
					continue;
				}
				
				if ($result->valid())
					continue;
				
				$validation_success = false;
				break;
			}
			
			$validation_error_method = $method_name.'__validation_error';
			echo 'validation_result: '.print_r($validation_result, 1);
		
			if (!$validation_success && method_exists($this->controller, $validation_error_method))	// if validation not success and __validation_error exist
			{
				echo "run $validation_error_method\n";
				return call_user_func_array(array($this->controller, $validation_error_method), array($validation_result));
			}
			
			if ('array' === $config['args'])
				$validation_result = array($validation_result);
		}
		
		$before_method = $method_name.'__before';
		if (method_exists($this->controller, $before_method))
		{
			$validation_result = call_user_func_array(array($this->controller, $before_method), $validation_result);
			
			if ('array' === $config['args'])
				$validation_result = array($validation_result);
		}
		
		$validation_result = call_user_func_array(array($this->controller, $method_name), $validation_result);
		
		$after_method = $method_name.'__after';
		if (method_exists($this->controller, $after_method))
		{
			if ('array' === $config['args'])
				$validation_result = array($validation_result);
				
			call_user_func_array(array($this->controller, $after_method), $validation_result);
		}
			
		return $this->controller->response;
	}
	
	protected function _parseConfig($config)
	{
		$params = explode(',', $config);
		$config = array();
		foreach ($params as $param)
		{
			$param = trim($param);
				
			list($name, $value) = explode(':', $param);
			$config[trim($name)] = trim($value);
		}
		
		return $config;
	}
}

class Validator
{
	protected $_value;
	protected $_pointers;
	protected $_callbacks;	
	
	protected $_optional = false;
	protected $_valid = true;
	protected $_rules = array();
	
	function __construct($value)
	{
		$this->_value = $value;
	}
	
	/**
	 * Return scalar value
	 * 
	 * @return scalar value 
	 */
	function value()
	{
		return $this->_value;
	}
	
	/**
	 * Set pointers for validation
	 * 
	 * @param array $pointers
	 * @return this 
	 */
	function pointers(&$pointers)
	{
		$this->_pointers =& $pointers;
		return $this;
	}
	
	/**
	 * Set object with callbacks functions
	 * 
	 * @param object $object
	 * @return this 
	 */
	function callbacks(&$object)
	{
		$this->_callbacks =& $object;
		return $this;
	}
	
	/**
	 * Set alias for rule
	 * 
	 * @param string $alias
	 * @return this 
	 */
	function alias($alias)
	{
		$result = array_pop($this->_rules);
		$this->_rules[$alias] = $result;
 
		return $this;
	}
	
	/**
	 * Is current value valid
	 * 
	 * @return bool 
	 */
	function valid()
	{
		return $this->_valid;
	}
	
	/**
	 * Return list of errors
	 * 
	 * @return array
	 */
	function errors()
	{
		$errors = array();
		foreach ($this->_rules as $name => $result)
		{
			if ($result) 
				continue;
			
			$errors[] = $name;
		}
		
		return $errors;
	}
	
	/**
	 * Add rule by string, parse and call method
	 * 
	 * @param string $rule
	 * @return this
	 */
	function rule($rule)
	{
		$alias = $rule;		
		if (preg_match_all('/(.+?)(\s+as\s+(\w+)|$)/i', $rule, $matches, PREG_SET_ORDER) && isset($matches[0][3]))	// if rule have alias
		{
			$rule = $matches[0][1];
			$alias = $matches[0][3];
		}
		 
		$args = array();		
		if (false !== ($spos = strpos($rule, '(')))	// if rule have arguments
		{		
			preg_match_all('/\((.*)\)/', $rule, $matches, PREG_SET_ORDER);	// separate rule name and rule arguments
			$buf = $matches[0][1];
			$rule = substr($rule, 0, $spos);
		 
			$code = '';			
			if (preg_match_all('/\$(\w+)/', $buf, $matches, PREG_SET_ORDER))	// if arguments have pointers
			{
				foreach ($matches as $match)
				{
					$pointer = $match[1];
					$code .= '$__param_'.$match[1].'=$this->_pointers[\''.$pointer.'\'];';
					$buf = str_replace($match[1], '__param_'.$match[1], $buf);
				}
			}
			$code .= '$args=array('.$buf.');';
			eval($code);	// build args
		}
		
		return $this->_callRule($rule, $args, $alias);
	}
	
	/**
	 * Add rules by array, each item is one rule
	 * 
	 * @param array $rules
	 * @return this
	 */
	function rules($rules)
	{
		foreach ($rules as $rule)
			$this->rule($rule);
		 
		return $this;
	}
	
	/**
	 * Call rule wich passed as string
	 * 
	 * @param string $rule
	 * @param array $args
	 * @param string $alias
	 * @return this
	 */
	protected function _callRule($rule, $args, $alias)
	{
		if ('call' === $rule)
		{
			$rule = $args[0];
			$args[0] = $this;
			$this->_addValidationResult($rule, call_user_method_array($rule, $this->_callbacks, $args));
		}
		else		
			call_user_method_array($rule, $this, $args);
		
		return $this->alias($alias);
	} 
	
	protected function _addValidationResult($rule, $result)
	{
		$this->_rules[$rule] = $result;
		if (false === $result) 
			$this->_valid = false;
			
		return $this;
	}
}