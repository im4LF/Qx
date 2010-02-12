<?php
class Mask_Router_Impl
{
	public $url;
	public $http_method;
	
	public $controller;
	public $method;
	public $view;
	public $params;
	
	function route($url, $http_method) 
	{
		$this->url = $url;
		$this->http_method = $http_method;
		
		$action_key = '__'.$this->url->action.'__'.$this->http_method.'_'.$this->url->viewtype;
		
		if (false === ($this->_findController()))
			return false;
					
		$this->_findMethodAndView($action_key);
		
		return $this;
	}
	
	protected function _findController()
	{
		$map = import::config('app:configs/router.php');
		foreach ($map as $regex => $this->controller) 
		{			
			if (preg_match($regex, $this->url->path))
				return true;
		}
		return false;
	}
	
	protected function _findMethodAndView($action_key)
	{
		$actions = get_class_vars($this->controller);
		$default = $buf = $actions['__x'];
		foreach ($actions as $action => $config)
		{
			if (0 !== strpos($action, '__') || '__x' === $action)
				continue;
			
			if (!preg_match('/'.str_replace('x', '[a-z0-9]+', $action).'/', $action_key)) 
				continue;

			$buf = $config;
			break;
		}

		if ('@' === $buf{0})
		{
			$this->controller = substr($buf, 1);
			return $this->_findMethodAndView($action_key);
		}
		
		$this->params = array_merge($this->_parseMethodConfig($default), $this->_parseMethodConfig($buf));
		$this->method = $this->params['m'];
		$this->view = $this->params['v'];
		
		return true;
	}
	
	protected function _parseMethodConfig($config)
	{
		$params = explode(',', $config);
		$config = array();
		foreach ($params as $param)
		{
			$param = trim($param);
				
			list($name, $value) = explode(':', $param);
			$config[trim($name)] = trim($value);
		}
		
		return $config;
	}
}
?>