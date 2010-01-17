<?php 
define('Q_PATH', realpath(dirname(__FILE__)));

spl_autoload_register(array('import', 'from'));

import::from('q:classes/utils/*');

class import
{
	protected static $_instance;
	protected $_config;
	protected $_data;
	protected $_stats;
	
	static function s()
	{
		if (!self::$_instance)
			self::$_instance = new self;
		
		return self::$_instance;
	}
	
	protected function __construct()
	{
		$this->configure(import::config('q:import.php'));
	}
	
	function configure($config = null)
	{
		if (!$config) 
			return $this->_config;
			
		$this->_config = $config;
		return $this;
	}
	
	static function from($mask)
	{
		$i = import::s();
		if (false === strpos($mask, '/'))
            $i->importClass($mask);
        else
            $i->importFiles($mask);
	}
	
	static function config($path)
	{
		list($type, $path) = explode(':', $path);
		$type_const = strtoupper($type).'_PATH';
		if (!defined($type_const)) 
			return;
			
		$config_file = constant($type_const).DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.$path;  
		
		if (!file_exists($config_file)) 
			return;
		
		return require($config_file);
	}
	
	function importClass($class_name)
    {
    	Benchmark::start("load class [$class_name]");
    	if ($this->_data['classes'][$class_name]['loaded'])	// class already loaded 
			return;
			
		$class_type = 'Class';
		if (false !== strpos($class_name, '_'))
		{
			$class_name_segments = explode('_', $class_name);
			$class_type = array_pop($class_name_segments);
		}
			
		$scan_directories = $this->_config['scanner']['classes'];
		$class_file = $class_name.'.class.php';
		
		if ('Scenario' === $class_type)
		{
			$scan_directories = $this->_config['scanner']['scenarios'];
			$class_file = $class_name_segments[0].'.scenario.php';
		}
		elseif ('Controller' === $class_type)
		{
			$scan_directories = $this->_config['scanner']['controllers'];
			$class_file = $class_name_segments[0].'.controller.php';
		}
		elseif ('Action' === $class_type)
		{
			$scan_directories = $this->_config['scanner']['controllers'];
			$class_file = $class_name_segments[0].'.actions'.DIRECTORY_SEPARATOR.$class_name_segments[0].'_'.$class_name_segments[1].'.action.php';
		}
		elseif ('Impl' === $class_type)
		{
			$scan_directories = $this->_config['scanner']['impls'];
			$class_file = $class_name_segments[0].'.impl.php';
		}
		
		$found = false;
		for ($i = 0; $i < count($scan_directories); $i++)
		{
			if (false === ($path = $this->_buildPath($scan_directories[$i])))
				continue;
			
			$path = $path.DIRECTORY_SEPARATOR.$class_file;
			
			if (!file_exists($path))
				continue;
				
			$found = true;
			break;
		}	
		
		//echo "$class_name: file - $class_file, type - $class_type, path - $path\n";
		
		if (!$found)
			throw new Exception("class [$class_name] not found.");	
		
		//echo "$class_name: $path\n";
		
		$this->_importFile($path);
		$this->_data['classes'][$class_name] = true;
		Benchmark::stop("load class [$class_name]");
	}
	
	function importFiles($path)
    {
        if (false === ($path = $this->_buildPath($path)))
			throw new Exception("type of [$path] not defined");
			
        if (strpos($path, '*'))	// import directory recursively
        {
        	foreach (glob($path) as $item)
			{
				if (!is_dir($item))	// its simple file
				{
					$this->_importFile($item);
					continue;
				}
				
				$di = new RecursiveDirectoryIterator($item);
				foreach (new RecursiveIteratorIterator($di) as $fileinfo)
					$this->_importFile($fileinfo->getPathname());
			}
        }
        else	// import simple file
            $this->_importFile($path.'.php');
    }
	
	protected function _buildPath($path)
	{
		list($type, $path) = explode(':', $path);
		
		$type_const = strtoupper($type).'_PATH';
		
		if (!defined($type_const))
			return false;
			
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $path = constant($type_const).DIRECTORY_SEPARATOR.$path;
		
		return $path;
	}
	
    protected function _importFile($path)
    {
    	if (!preg_match('/\.php$/', $path) || isset($this->_data['files'][$path])) 
			return;

		require($path);		
		$this->_data['files'][$path] = true;
    }
}
?>