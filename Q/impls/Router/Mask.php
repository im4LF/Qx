<?php

class iRouter_Mask
{
	public $request;
	public $controller;
	
	function __construct(&$request) 
	{
		$this->request =& $request;
	}
	
	function route() 
	{
		$path = $this->request->url->path;
		
		$map = import::config('app:router.php');
		
		foreach ( $map as $regex => $controller ) 
		{
			if ( !preg_match($regex, $path) ) continue;
			
			break;
		}
		
		$this->controller = $controller.'_Controller';
		return $this;
	}
}
?>