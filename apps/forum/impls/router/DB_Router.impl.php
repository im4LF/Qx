<?php
class DB_Router_Impl
{
	public $url;
	public $request_method;
	
	public $controller;
	public $method;	
	public $view;
	public $data;
	
	function route($url, $request_method)
	{
		$this->url = $url;
		$this->request_method = $request_method;
				
		$action = preg_replace('/(-|_)+/', '_', $this->url->action);
		$amv = '__'.$action.'__'.$this->request_method.'_'.$this->url->viewtype;
		
		if (false === ($this->_findController()))
			return false;
				
		$this->_findMethodAndView($amv);
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
	
	protected function _findMethodAndView($amv)
	{
		$actions = get_class_vars($this->controller);
		$default = $buf = $actions['__x'];
		foreach ($actions as $action => $config)
		{
			if (0 !== strpos($action, '__') || '__x' === $action)
				continue;
			
			if (!preg_match('/'.str_replace('_x', '_[a-z0-9_]+', $action).'/', $amv)) 
				continue;

			$buf = $config;
			break;
		}
		
		if ('@' === $buf{0})
		{
			if ($this->controller !== substr($buf, 1))
			{
				$this->controller = substr($buf, 1);
				return $this->_findMethodAndView($amv);
			}
			else
				$buf = $default; 
		}
			
		@list($this->method, $this->view) = explode(':', $buf);
		
		$no_method = empty($this->method);
		$no_view = empty($this->view);
		if ($no_method || $no_view)
		{
			@list($default_method, $default_view) = explode(':', $default);
			if ($no_method)
				$this->method = $default_method;
			 
			if ($no_view)
				$this->view = $default_view;
		}
		
		return true;
	}
}