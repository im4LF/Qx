<?php
class Mask_Router_Impl
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
		
		foreach ($map as $regex => $controller) 
		{
			if (!preg_match($regex, $path)) 
				continue;
			
			$this->controller = $controller;
			break;
		}
		
		return $this;
	}
}
?>