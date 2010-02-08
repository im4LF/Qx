<?php
class DB_Router_Impl
{
	public $url;
	public $http_method;
	
	public $controller;
	public $method;
	public $view;
	public $data;
	
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
		$db = R('db');
		
		$q = 'SELECT * FROM `#nodes` WHERE path = ?s';
		if (!$db->query($q, array($this->url->path))->ok())
			throw new Exception(print_r($db->error(), 1));

		if (!$db->numRows())
			return false;
			
		$this->data = $db->fetch();
		$this->controller = $this->data['controller'].'_Controller';
		
		return true;
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
		
		list($this->method, $this->view) = explode(':', $buf);
		$no_method = empty($this->method);
		$no_view = empty($this->view);
		if ($no_method || $no_view)
		{
			list($default_method, $default_view) = explode(':', $default);
			if ($no_method)
				$this->method = $default_method;
				
			if ($no_view)
				$this->view = $default_view;
		}
		return true;
	}
}