<?php
class Runner_Impl
{
	public $request;
	public $action;
	public $method;
	public $result;
	public $controller_name;
	public $controller_file;
	
	protected $_action_key;
	protected $_parser;
	
	function __construct(&$request)
	{
		$this->request =& $request;
	}
	
	function run()
	{
		$this->controller_name = $this->request->router->controller;
		$this->controller_file = import::getClassFile($this->controller_name);
		import::from($this->controller_name);
		
		$this->_action_key = $this->request->method.':'.$this->request->url->action.'.'.$this->request->url->view;
		
		$this->_parser = F::n('DocCommentParser');					// init controller configuration parser
		$this->_findControllerAndMethod($this->controller_name);	// find real controller name and method for requested action
		$this->_parser = null;
		
		$controller = F::n($this->controller_name, $this->request);	// create controller object
		
		// build method args array
		$method_args = array();
		if (isset($this->method['params']))
		{
			foreach ($this->method['params'] as $name => $params)
			{
				$method_args[$name] = F::n($params['type'], $this->request->data($name, $params['from']));
			}
		}

		$validation_config = $this->method['configs']['validation'];	// get validation configuration
		$method_args_validation = array();
		
		// if validation is enabled
		if (isset($validation_config['on']))
		{
			if (isset($validation_config['auto'])) // auto validation enabled
			{
				$have_errors = false;
				foreach ($this->method['params'] as $name => $params)
				{
					$validation_result = Validator::init(
						$method_args[$name],	// current value for validation 
						$method_args,			// array with pointers
						$controller				// object with defined callback functions
					)->rules($params['rules'])->validate();
					
					if ($validation_result->haveErrors())
						$have_errors = true;
						
					$method_args_validation[$name] = $validation_result;
				}
				
				// if validation have errors and auto validation not "soft" - call validation_error method
				if ($have_errors && !isset($validation_config['auto']['soft']))
				{
					$this->result = call_user_method_array($this->action['method'].'__validation_error', $controller, array($method_args_validation));
					unset($controller);
					return $this;
				}
			}
			
			if (isset($validation_config['user'])) // user validation enabled
			{
				// call user-defined validation method
				$method_args_validation = call_user_method_array($this->action['method'].'__validate', $controller, array($method_args_validation));
				$have_errors = false;
				foreach ($method_args_validation as $field)
				{
					if (!$field->haveErrors()) 
						continue;
					
					$have_errors = true;
					break;
				}
				
				// if result is false and user-defined validation not "soft"
				if ($have_errors && !isset($validation_config['user']['soft']))
				{
					$this->result =  call_user_method_array($this->action['method'].'__validation_error', $controller, array($method_args_validation));
					unset($controller);
					return $this;
				}
			}
		}
		
		// if defined before methods
		if (count($this->action['params']['before']))
		{
			foreach ($this->action['params']['before'] as $method => $buf)
				call_user_method($method, $controller);
		}
		
		// call method
		$this->result = call_user_method_array($this->action['method'], $controller, $method_args);
		
		// if defined after methods
		if (count($this->action['params']['after']))
		{
			foreach ($this->action['params']['after'] as $method => $buf)
				call_user_method($method, $controller);
		}
		
		unset($controller);
		return $this;
	}
	
	/**
	 * 
	 * @param string $controller_name
	 * @return 
	 */
	protected function _findControllerAndMethod($controller_name)
	{
		if (!isset(F::s('Configs')->controllers[$controller_name]))
		{
			$cache_validation_key = filemtime($this->controller_file);
			
			if (null === ($buf = F::r('Cache:controllers')->get($controller_name, $cache_validation_key)->value))
			{
				echo "$controller_name: write cache\n";
				$buf = $this->_parser->parse($controller_name);
				F::s('Configs')->controllers[$controller_name] = $buf;
				F::r('Cache:controllers')->set($controller_name, $buf, $cache_validation_key);
			}
			else
			{
				echo "$controller_name: read cache\n";
				F::s('Configs')->controllers[$controller_name] = $buf;
			}
		}
		
		$controller_config = F::s('Configs')->controllers[$controller_name];
		
		$finded_action = null;
		foreach ($controller_config['actions'] as $action)
		{
			if (!preg_match($action['regex'], $this->_action_key)) 
				continue;
			
			$finded_action = $action;
		}
		
		$this->action = $finded_action;
		$this->method = $controller_config['methods'][$this->action['method']];
		
		if ('@' === $finded_action['method']{0})
		{
			$this->controller_name = substr($finded_action['method'], 1);
			$this->_findControllerAndMethod($this->controller_name);
		}	
	}
}
?>