<?php

class MaskRouter 
{
	protected $_request;
	
	function __construct(&$request) 
	{
		$this->_request =& $request;
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
		
		$controller .= '_Controller';
		return array(
			'controller' => $controller 
		);
	}
}
?>