<?php
class FileCache_Impl
{
	protected $_cache_file;
	protected $_keys;
	
	function __construct($config)
	{
		$this->_cache_file = $config['file'];
		return $this;
	}
	
	function get($key)
	{
		if (!isset($this->_keys[$key]))
			return false;
			
		return $this->_keys[$key];
	}
	
	function set($key, $value = null)
	{
		$this->_keys[$key] = $value;
	}
	
	function load()
	{
		$this->_keys = unserialize(file_get_contents($this->_cache_file));
		return $this;
	}
	
	function save()
	{
		file_put_contents($this->_cache_file, serialize($this->_keys));
		return $this;
	}
}
?>