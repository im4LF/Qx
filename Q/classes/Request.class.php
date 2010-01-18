<?php
class Request 
{
	public $raw_url;				// requested url
	public $method = 'GET';			// request method - default "GET"
	public $name;					// scenario name - for identification if responses
	public $scenario = 'internal';	// scenario name, default - "internal"
	public $get;					// $_GET values
	public $post;					// $_POST values
	public $files;					// $_FILES values
	
	function __construct($url, $params = array()) 
	{
		$this->raw_url = $url;
		$this->name = $this->raw_url;
		
		foreach ($params as $name => $value)
		{
			$this->$name = $value;
		}
		
		$this->method = strtolower($this->method);
	}
	
	function data($name, $from)
	{
		if ('args' == $from)
			return $this->url->args[$name];
		
		return $this->$from[$name];
	}
	
	function dispatch() 
	{
		$scenario = QF::s('Configs')->scenarios[$this->scenario];	// get scenario name
		
		QF::n($scenario, $this)	// create scenario
			->init()			// init scenario
			->run()				// run
			->done();			// and done
	}
}
?>