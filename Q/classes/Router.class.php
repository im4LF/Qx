<?php
class Router 
{
	protected $_request;
	protected $_properties;
	
	function __construct(&$request) 
	{
		$this->_request =& $request;
	}
	
	function __get($name) 
	{
		return $this->_properties[$name];
	}
	
	function route()
	{
		$impl = QF::s('Configs')->impls['router'];
		$this->_properties = QF::n($impl, $this->_request)->route();
		return $this;
	}
}
?>