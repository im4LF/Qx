<?php

class MaskRouter 
{
	protected $_request;
	protected $_controller;
	
	function __construct(&$request) 
	{
		$this->_request =& $request;
	}
	
	function __get($name) 
	{
		return $this->{'_'.$name};
	}
	
	function route() 
	{
		$path = $this->_request->url->path;
		$map = import::config('app:router.php');
		
		foreach ( $map as $regex => $controller ) 
		{
			if ( !preg_match($regex, $path) ) continue;
			
			break;
		}
		
		$this->_controller = $controller.'_Controller';
		return $this;
	}
}
?>