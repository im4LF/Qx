<?php
class Mask_Router_Impl
{
	public $url;
	public $http_method;
	
	public $controller;
	public $method;
	public $method_config;
	public $view;
	
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
			return $this->_findMethodAndView($action_key);
		}
		
		$buf = $this->_parseActionConfig($buf);
		
		$this->method_config = $buf;
		$this->method = $buf['method'];
		$this->view = $buf['view'];
		
		$no_method = empty($this->method);
		$no_view = empty($this->view);
		if ($no_method || $no_view)
		{
			$buf = $this->_parseActionConfig($default);
			$this->method_config = $buf;
			
			if ($no_method)
				$this->method = $buf['method'];
				
			if ($no_view)
				$this->view = $buf['view'];
		}
		
		print_r($this);
		
		return true;
	}
	
	protected function _parseActionConfig($string)
	{
		$params = explode(';', $string);
		foreach ($params as $param)
		{
			$param = trim($param);
			list($key, $values) = explode(':', $param);
			$key = trim($key);
			$values = trim($values);
			
			if (false !== strpos($values, ' '))
			{
				$values = array_fill_keys(explode(' ', $values), true);
			}
			$config[$key] = $values;
		}
		
		return $config;
	}
}
?>