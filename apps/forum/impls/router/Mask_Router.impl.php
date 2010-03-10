<?php
class Mask_Router_Impl
{
	public $url;
	public $request_method;
	
	public $amv;
	public $controller;
	public $method;
	public $view;
	
	function route($url, $request_method) 
	{
		$this->url = $url;
		$this->request_method = $request_method;
		
		$action = preg_replace('/(-|_)+(\w)/e', 'strtoupper("\2")', $this->url->action);
		echo "action: $action\n";
		$this->amv = '___'.$action.'_'.$this->request_method.'_'.$this->url->viewtype;
		
		if (false === ($this->_findController()))
			return false;
				
		//$this->_findMethodAndView($amv);
		$this->method = $this->_findMethod();
		$this->view = $this->_findView();
		
		echo print_r($this,1);
		exit;
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
	
	protected function _findMethod()
	{
		$actions = array_fill_keys(get_class_methods($this->controller), true);
		$default = $current = '___x';
		unset($actions['___x']);
		echo 'actions: '.print_r($actions, 1);
		foreach ($actions as $action => $buf)
		{
			if (0 !== strpos($action, '___'))
				continue;
			
			$re = preg_replace('/_at_(.*)/', '_x', $action);
			$re = '/'.str_replace('_x', '_[a-z0-9]+', $re).'/';
			echo "amv: {$this->amv}, re: $re\n";
			if (!preg_match($re, $this->amv)) 
				continue;
			
			$current = $action;
			break;
		}
		
		if (false !== ($pos = strpos($current, '_at_')))
		{
			$this->controller = substr($current, $pos+4);
			return $this->_findMethod();
		}
		
		return $current;
	}
	
	protected function _findView()
	{
		$views = get_class_vars($this->controller);
		return array_key_exists($this->method, $views) ? $views[$this->method] : $views['___x'];
	}
	
	/*protected function _findMethodAndView($action_key)
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
	}*/
}
?>