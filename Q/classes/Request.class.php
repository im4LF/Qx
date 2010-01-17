<?php
class Request 
{
	protected $_raw_url;				// requested url
	protected $_method = 'GET';			// request method - default "GET"
	protected $_get;					// $_GET values
	protected $_post;					// $_POST values
	protected $_files;					// $_FILES values
	protected $_name;
	protected $_scenario = 'internal';	// scenario name, default - "internal"
	
	function __construct($url, $params = array()) 
	{
		$this->_raw_url = $url;
		$this->_name = $this->_raw_url;
		
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
		
		return $this->{'_'.$from}[$name];
	}
	
	function __get($name) 
	{
		return $this->{'_'.$name};
	}
	
	function dispatch() 
	{
		QF::s('RequestManager')->addRequest($this);					// add new Request to RequestManager
		$scenario = QF::s('Configs')->scenarios[$this->_scenario];	// get scenario name
		QF::n($scenario, $this)->run();								// run scenario
	}
}
?>