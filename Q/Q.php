<?php
$__r = array();	// registry holder
$__s = array(); // sigleton holder

import::register();

class import
{
	private static $_config;
	private static $_data;
	private static $_counter;
	
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
	 * @example import::from('q:classes/utils/F.class.php'); - load files
	 * @example import::from('Request'); - load Request class
	 * 
	 * @param object $mask
	 * @return 
	 */
	static function from($mask)
	{
		return false === strpos($mask, '/') ? import::importClass($mask) : import::importFiles($mask);   
	}
	
	/**
	 * Import configuration
	 * 
	 * @example import::config('q:path/to/config.file'); - load from Q_PATH/configs/path/to/config.file
	 * 
	 * @param string $path
	 * @return array
	 */
	static function config($path)
	{
		$key = $path;
		
		if (self::$_data['configs'][$key]['loaded'])
			return self::$_data['configs'][$key]['data'];
			
		if (false === ($path = self::buildPath($path)) || !file_exists($path))
			return;
		
		self::$_data['configs'][$key]['loaded'] = true;		
		self::$_data['configs'][$key]['data'] = (object) require($path);
		return self::$_data['configs'][$key]['data'];
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
    	if (!preg_match(self::$_config['import']['mask'], $path) || isset(self::$_data['files'][$path])) 
			return;

		require($path);		
		self::$_data['files'][$path] = true;
    }
}

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

function S()
{
	global $__s;
	
	$args = func_get_args();
    $class_name = $args[0];
	
    if (!isset($__s[$class_name]))
        $__s[$class_name] = call_user_func_array('F', $args);

    return $__s[$class_name];
}

/*function R()
{
	global $__fsr;
	
	$args = func_get_args();
	$class_key = $args[0];
	$args[0] = substr($class_key, 0, strpos($class_key, ':'));
	if (!isset($__fsr[$class_key]))
        $__fsr[$class_key] = call_user_func_array('F', $args);

    return $__fsr[$class_key];
}*/

function R($key, $value = null)
{
	global $__r;
	
	if (!isset($__r[$key]))
        $__r[$key] = $value;

    return $__r[$key];
}

class Benchmark
{
	static $marks;
	
	static function start($key)
	{
		self::$marks[$key]->t0 = microtime(true);
		
		return $this; 
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

class Request 
{
	protected $raw_url;				// requested url
	protected $method = 'GET';			// request method - default "GET"
	protected $name;					// scenario name - for identification in responses
	protected $scenario = 'internal';	// scenario name, default - "internal"
	protected $cookie;					// $_COOKIE values
	protected $get;					// $_GET values
	protected $post;					// $_POST values
	protected $files;					// $_FILES values
	
	function __construct($url, $params = array()) 
	{
		$this->raw_url = $url;
		$this->name = $this->raw_url;
		
		foreach ($params as $name => $value)
			$this->$name = $value;
		
		$this->method = strtolower($this->method);
	}
	
	function data($name, $from)
	{
		if ('url' === $name)
			return $this->url->args[$key];
			
		return $this->{$from}[$key];
	}
	
	function dispatch() 
	{
		$scenarios = import::config('app:configs/app.php')->scenarios;	// get scenario name
		print_r($scenarios);
		
		/*F($config['scenarios'][$this->scenario], $this)	// create scenario
			->open()			// init scenario
			->run()				// run
			->close();			// close*/
	}
}

class Runner
{
	public $request;
	public $method_name;
	public $view_name;
	public $result;
	
	function __construct(&$request)
	{
		$this->request =& $request;
	}
	
	function run()
	{
		$controller_name = $this->request->router->controller;
		$action_key = $this->request->method.':'.$this->request->url->action.'.'.$this->request->url->view;
		
		$controller = $this->_getController($controller_name, $action_key);
		
		$validate_method_name = $this->method_name.'__validate';
		
		if (!method_exists($controller, $validate_method_name))
			$this->result = $controller->{$this->method_name}();
		else
		{
			$validation_success = true;
			$validation_result = $controller->$validate_method_name();
			foreach ($controller->$validate_method_name() as $result)
			{
				if ($result->haveErrors())
					continue;
					
				$validation_success = false;
				break;
			}
			
			$validation_error_method_name = $this->method_name.'__validation_error';
			
			if (!$validation_success && method_exists($controller, $validation_error_method_name))
				$this->result = call_user_func_array(array($controller, $validation_error_method_name), array($validation_result));
			else
			{
				$params = array();
				foreach ($validation_result as $param)
					$params[] = $param->value();
					
				$this->result = call_user_func_array(array($controller, $this->method_name), $params);
			}
		}	
		
		return $this;
	}
	
	protected function  _getController($controller_name, $action_key)
	{
		$controller = F($controller_name, $this->request);
		$actions = $controller->__actions();
		
		foreach ($actions as $mask => $method_view)
		{
			$regex = str_replace(':', '\:', $mask);
			$regex = str_replace('.', '\.', $regex);
			$regex = '/'.str_replace('*', '[\w\-]+', $regex).'/';
			
			if (!preg_match($regex, $action_key)) 
				continue;
			
			$buf = $method_view;
		}
		
		if ('@' === $buf{0})
			return $this->_getController(substr($buf, 1), $action_key);
		
		list($this->method_name, $this->view_name) = explode(':', $buf);
		
		return $controller;
	}
}

class Validator
{
	protected $_value;
	protected $_pointers;
	protected $_callback;
	
	protected $_rules;
	protected $_errors;
	
	function __construct($value, $type)
	{
		$this->_value = new $type($value);
		return $this;
	}
	
	static function init($value, $type)
	{
		return new Validator($value, $type);
	}
	
	function pointers(&$pointers)
	{
		$this->_pointers =& $pointers;
		return $this;
	}
	
	function callbacks(&$object)
	{
		$this->_callbacks =& $object;
		return $this;
	}
	
	function value()
	{
		return $this->_value;
	}
	
	function rule($rule)
	{
		$alias = $rule;
		// if rule have alias
		if (preg_match_all('/(.+?)(\s+as\s+(\w+)|$)/i', $rule, $matches, PREG_SET_ORDER) && isset($matches[0][3]))
		{	
			$alias = $matches[0][3];
			$rule = $matches[0][1];
		}
		
		$args = array();
		if (false !== ($spos = strpos($rule, '('))) // if rule have arguments
		{	
			// separate rule name and rule arguments
			preg_match_all('/\((.*)\)/', $rule, $matches, PREG_SET_ORDER);
			$buf = $matches[0][1];
			$rule = substr($rule, 0, $spos);
			
			// make code for build arguments array 
			$code = '';
			if (preg_match_all('/\$(\w+)/', $buf, $matches, PREG_SET_ORDER)) // if arguments have pointers
			{
				foreach ($matches as $match)
				{
					$pointer = $match[1];
					$code .= '$__param_'.$match[1].'=$this->_pointers[\''.$pointer.'\'];';
					$buf = str_replace($match[1], '__param_'.$match[1], $buf);
				}
			}			
			$code .= '$args=array('.$buf.');';
			eval($code); // build args
		}
		
		$this->_addRule($rule, $args, $alias);
		
		return $this;
	}
	
	function rules($rules = null)
	{
		if (!$rules)
			return $this->_rules;
			
		foreach ($rules as $rule)
			$this->rule($rule);
			
		return $this;
	}
	
	protected function _addRule($name, $args, $alias)
	{
		$at = '_value';
		if ('call' === $name)
		{
			$at = '_callbacks';
			$name = $args[0];
			$args[0] = $this->_value;
		}
		
		$this->_rules[$alias] = array(
			'at' => $at,
			'name' => $name,
			'args' => $args
		);
	}
	
	function __call($name, $args)
	{
		$this->_addRule($name, $args, $name);
		
		return $this;
	}
	
	function _as($alias)
	{
		$rule = array_pop($this->_rules);
		$this->_rules[$alias] = $rule;
		
		return $this;
	}
	
	function validate()
	{
		foreach ($this->_rules as $alias => $params)	// foreach rules
		{
			if (isset($params['validated'])) continue;	// if rule already validated
			
			$result = call_user_method_array($params['name'], $this->{$params['at']}, $params['args']);
			$this->_rules[$alias]['result'] = $result;
			$this->_rules[$alias]['validated'] = true;
			
			if (!$result)	// if result is error
				$this->_errors[$alias] = true;	// save error
		}

		return $this;	
	}
	
	function errors()
	{
		return $this->_errors;
	}

	function haveErrors()
	{
		return (bool) count($this->_errors);
	}
}