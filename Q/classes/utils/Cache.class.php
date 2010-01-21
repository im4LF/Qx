<?php
class Cache
{
	protected $_impl;
	protected $_sections;
	
	/**
	 * Initialize cache
	 * 
	 * @param string $impl - implementation class name, 
	 * 						 if null of false - implementation will get from configuration
	 * @param array $config - configuration
	 * @return 
	 */
	function __construct($config)
	{
		$impl = $config['impl'];
		$this->_impl = F::n($impl, $config);
	}
	
	/**
	 * Get value from section:key
	 * 
	 * @param string $key section-name:key-name
	 * @param object $validation_key [optional] if passed - make comparison validation keys
	 * @return object with properties: 'value' - cached value, validation_key - validation key of value
	 */
	function get($key, $validation_key = null)
	{
		$section = 'global';
		if (false !== strpos($key, ':'))
			list($section, $key) = explode(':', $key);
		
		if (!isset($this->_sections[$section][$key]))
			return false;
		
		if (null !== $validation_key && $this->_sections[$section][$key]->validation_key !== $validation_key)
			return (object) array(
				'value' => null,
				'validation_key' => $this->_sections[$section][$key]->validation_key
			);
			
		return $this->_sections[$section][$key];
	}
	
	/**
	 * Set value to section:key
	 * 
	 * @param string $key section-name:key-name
	 * @param object $value
	 * @param object $validation_key [optional] need to validation cached value
	 * @return 
	 */
	function set($key, $value, $validation_key = true)
	{
		$section = 'global';
		if (false !== strpos($key, ':'))
			list($section, $key) = explode(':', $key);
			
		$this->_sections[$section][$key] = (object) array(
			'value' => $value,
			'validation_key' => $validation_key
		);
		
		return $this;
	}
	
	/**
	 * Delete value from section:key
	 * 
	 * @param string $key
	 * @return $this
	 */
	function delete($key)
	{
		list($section, $key) = explode(':', $key);
		unset($this->_sections[$section][$key]);
		
		return $this;
	}
	
	/**
	 * Load section by name
	 * 
	 * @param string $section
	 * @return $this
	 */
	function load()
	{
		$this->_sections = $this->_impl->load();
		return $this;
	}
	
	/**
	 * Save section by name or save all if section name not passed 
	 * 
	 * @param string $section [optional]
	 * @return $this
	 */
	function save()
	{
		$this->_impl->save($this->_sections);
		return $this;
	}
}
?>