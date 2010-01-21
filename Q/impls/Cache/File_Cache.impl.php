<?php
class File_Cache_Impl
{
	protected $_config;
	
	function __construct($config)
	{
		$this->_config = $config;
	}
	
	function load()
	{
		$filename = import::buildPath($this->_config['file']);
		if (!file_exists($filename))
			return false;
			
		return unserialize(file_get_contents($filename));
	}
	
	function save($values)
	{
		$filename = import::buildPath($this->_config['file']);
		file_put_contents($filename, serialize($values));
	}
}
?>