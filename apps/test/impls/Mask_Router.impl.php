<?php
class Mask_Router_Impl
{
	public $request;
	public $controller;
	public $method;
	public $view;
	
	function __construct(&$request) 
	{
		$this->request =& $request;
	}
	
	function route() 
	{
		$action_key = '__'.$this->request->url->action.'__'.$this->request->method.'_'.$this->request->url->view;
		$this->controller = $this->_getController();		
		$this->method = $this->_getMethod($action_key);
		return $this;
	}
	
	protected function _getController()
	{
		$path = $this->request->url->path;
		
		$map = import::config('app:configs/router.php');
		foreach ($map as $regex => $controller) 
		{
			if (preg_match($regex, $path)) 
				break;
		}
		return $controller;
	}
	
	protected function _getMethod($action_key)
	{
		$actions = get_class_vars($this->controller);
		
		$default = $buf = $actions['__x'];
		foreach ($actions as $action => $method_view)
		{
			if (0 !== strpos($action, '__') || '__x' === $action)
				continue;
				
			if (!preg_match('/'.str_replace('x', '[a-z0-9]+', $action).'/', $action_key)) 
				continue;
			
			$buf = $method_view;
			break;
		}
		
		if ('@' === $buf{0})
		{
			$this->controller = substr($buf, 1);
			return $this->_getMethod($action_key);
		}
		
		list($method, $this->view) = explode(':', $buf);
		$no_method = empty($method);
		$no_view = empty($this->view);
		if ($no_method || $no_view)
		{
			list($default_method, $default_view) = explode(':', $default);
			if ($no_method)
				$method = $default_method;
				
			if ($no_view)
				$this->view = $default_view;
		}
		
		return $method;
	}
}
?>