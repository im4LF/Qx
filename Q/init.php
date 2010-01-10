<?php 
define('Q_PATH', realpath(dirname(__FILE__)));

import::from('q:utils/*');

class import
{
	protected static $_instance;
	protected $_config;
	protected $_data;
	
	static function s()
	{
		if (!self::$_instance)
			self::$_instance = new self;
		
		return self::$_instance;
	}
	
	protected function __construct()
	{
		$this->configure(import::config('q:import.php'))->scan();
	}

	function configure($config = null)
	{
		if (!$config) 
			return $this->_config;
			
		$this->_config = $config;
		return $this;
	}
	
	function scan()
	{
		if (($this->_data = $this->_cache())) return;
		
		for ($i = 0; $i < count($this->_config['scanner']['directories']); $i++)
		{
			list($type, $directory) = explode(':', $this->_config['scanner']['directories'][$i]);
			$type_const = strtoupper($type).'_PATH';
			if (!defined($type_const)) continue;
			
			$path = constant($type_const).DIRECTORY_SEPARATOR.$directory;
			$this->_scanDirectory($path);
		}
		
		$this->_cache($this->_data);
	}
	
	protected function _cache($value = null)
	{
		$cache_file = $this->_config['cache-file'];
		
		list($type, $cache_file) = explode(':', $cache_file);
		$type_const = strtoupper($type).'_PATH';
		if (!defined($type_const)) return false;
		
		$cache_file = constant($type_const).DIRECTORY_SEPARATOR.$cache_file;
		
		if (!$value && !file_exists($cache_file))
			return false;
			
		if ($value) // save to cache
		{
			file_put_contents($cache_file, serialize($value));
			return true;
		}
		
		return unserialize(file_get_contents($cache_file));
	}
	
	protected function _scanDirectory($path)
	{
		if (!file_exists($path)) return;
		
		$di = new RecursiveDirectoryIterator($path);
		foreach (new RecursiveIteratorIterator($di) as $fileinfo)
		{
			$filename = $fileinfo->getPathname();
			
			if (!preg_match($this->_config['scanner']['filenames'], $filename)) continue;
				
			$content = file_get_contents($filename);
            if (!preg_match_all('/^\s*class\s+(\w+)/im', $content, $matches, PREG_SET_ORDER)) continue;
               
			$this->_data->files[$filename] = @$this->_data->files[$filename];
			    
            foreach ($matches as $match)
            {
                /*$this->_data->classes[$type.':'.$match[1]] = (object) array(
					'name' => $match[1],
					'path' => $filename,
					'loaded' =>& $this->_data->files[$filename],
				);*/
				$this->_data->classes[$match[1]] = (object) array(
					'name' => $match[1],
					'path' => $filename,
					'loaded' =>& $this->_data->files[$filename],
				);
            }
		}			
	}
	
	static function from($mask)
	{
		$i = import::s();
		if (false === strpos($mask, '/'))
        {
            $i->importClass($mask);
        }
        else
        {
            $i->importFiles($mask);
        }
	}
	
	static function config($path)
	{
		list($type, $path) = explode(':', $path);
		$type_const = strtoupper($type).'_PATH';
		if (!defined($type_const)) return;
			
		$config_file = constant($type_const).DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.$path;  
		
		if (!file_exists($config_file)) return;
		
		return require($config_file);
	}
	
	function importClass($class_name)
    {
		if (!array_key_exists($class_name, $this->_data->classes))	// class not found
			throw new Exception("class [$class_name] not found");

		if ($this->_data->classes[$class_name]->loaded) return;		// class already loaded 
 
		require($this->_data->classes[$class_name]->path);			// load file
		$this->_data->classes[$class_name]->loaded = true;
	}
	
	function importFiles($path)
    {
        list($type, $path) = explode(':', $path);
		
		$type_const = strtoupper($type).'_PATH';
		
		if (!defined($type_const)) 
			throw new Exception($type.' not defined');
			
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $path = constant($type_const).DIRECTORY_SEPARATOR.$path;

        if (strpos($path, '*'))				// import directory recursively
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
        else								// import simple file
            $this->_importFile($path.'.php');
    }
	
    protected function _importFile($path)
    {
    	if (!preg_match('/\.php$/', $path) || isset($this->_data->files[$path])) return;

		require($path);		
		$this->_data->files[$path] = true;
    }
}

spl_autoload_register(array('import', 'from'));
?>