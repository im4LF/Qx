<?php
class Request 
{
	protected $_raw_url;				// requested url
	protected $_method = 'GET';			// request method - default "GET"
	protected $_get;					// $_GET values
	protected $_post;					// $_POST values
	protected $_files;					// $_FILES values
	protected $_scenario = 'internal';	// scenario name, default - "internal"
	
	function __construct($url, $params = array()) 
	{
		$this->_raw_url = $url;
		
		foreach ($params as $name => $value)
		{
			$this->{'_'.$name} = $value;
		}
		
		$this->_method = strtolower($this->_method);
	}
	
	function data($name, $from)
	{
		if ('args' == $from)
			return $this->url->args[$name];
		if ('get' == $from)
			return $this->_get[$name];
		if ('post' == $from)
			return $this->_post[$name];
		if ('files' == $from)
			return $this->_files[$name];
	}
	
	function __get($name) 
	{
		return $this->{'_'.$name};
	}
	
	function dispatch() 
	{
		$manager = QF::s('RequestManager');	// get RequestManager
		$manager->pushRequest($this);		// push current request to RequestManager stack
		
		// get scenario and run it
		$scenario = QF::s('Configs')->scenarios[$this->_scenario];
		QF::n($scenario, $this)->run();
		
		$manager->popRequest();				// pop current request from stack
	}
}
?>