<?php
class Runner
{
	protected $_request;
	protected $_parser;
	protected $_controller;
	protected $_action;
	protected $_method;
	
	function __construct(&$request)
	{
		$this->_request =& $request;
	}
	
	function run()
	{
		$this->_controller =& $this->_request->router->controller;
		//echo "controller: {$this->_controller}\n";
		import::from($this->_controller);
		
		// load controller and find method for action
		$this->_parser = QF::n(QF::s('Configs')->impls['parser']);
		$this->_loadController($this->_controller);
		$this->_parser = null;
		
		$this->_controller = new $this->_controller($this->_request);
		
		// build method args array
		$method_args = array();
		if (isset($this->_method['params']))
		{
			foreach ($this->_method['params'] as $name => $params)
			{
				//$method_args[$name] = new $params['type']($this->_request->{$params['from']}[$name]);
				$method_args[$name] = new $params['type']($this->_request->data($name, $params['from']));
			}
		}

		// validation
		$validation_config = $this->_method['configs']['validation'];
		$method_args_validation = array();
		
		// if validation is enabled
		if (isset($validation_config['on']))
		{
			if (isset($validation_config['auto'])) // auto validation enabled
			{
				$have_errors = false;
				foreach ($this->_method['params'] as $name => $params)
				{
					$result = Validator::init(
						$method_args[$name],	// current value for validation 
						$method_args,			// array with pointers
						$this->_controller		// object with defined callback functions
					)->rules($params['rules'])->validate();
					
					if ($result->haveErrors())
						$have_errors = true;
						
					$method_args_validation[$name] = $result;
				}
				
				// if validation have errors and auto validation not "soft" - call validation_error method
				if ($have_errors && !isset($validation_config['auto']['soft']))
				{
					// call validation_error method
					return call_user_method_array($this->_action['method'].'__validation_error', $this->_controller, array($method_args_validation));
				}
			}
			
			if (isset($validation_config['user'])) // user validation enabled
			{
				// call user-defined validation method
				$method_args_validation = call_user_method_array($this->_action['method'].'__validate', $this->_controller, array($method_args_validation));
				$have_errors = false;
				foreach ($method_args_validation as $field)
				{
					if (!$field->haveErrors()) continue;
					
					$have_errors = true;
					break;
				}
				
				// if result is false and user-defined validation not "soft"
				if ($have_errors && !isset($validation_config['user']['soft']))
				{
					// call validation_error method
					return call_user_method_array($this->_action['method'].'__validation_error', $this->_controller, array($method_args_validation));
				}
			}
		}
		
		// if defined before methods
		if (count($this->_action['params']['before']))
		{
			foreach ($this->_action['params']['before'] as $method => $buf)
				call_user_method($method, $this->_controller);
		}
		
		// call method
		$res = call_user_method_array($this->_action['method'], $this->_controller, $method_args);
		
		// if defined after methods
		if (count($this->_action['params']['after']))
		{
			foreach ($this->_action['params']['after'] as $method => $buf)
				call_user_method($method, $this->_controller);
		}
		
		return $res;
	}
	
	protected function _loadController($controller)
	{
		if (!isset(QF::s('Configs')->controllers[$controller]))
		{
			QF::s('Configs')->controllers[$controller] = $this->_parser->parse($controller);
		}
		
		$controller_config = QF::s('Configs')->controllers[$controller];
		
		$action_key = $this->_request->method.':'.$this->_request->url->action.'.'.$this->_request->url->view;
		$finded_action = null;
		foreach ($controller_config['actions'] as $action)
		{
			if (!preg_match($action['regex'], $action_key)) continue;
			
			$finded_action = $action;
		}
		
		
		$this->_action = $finded_action;
		$this->_method = $controller_config['methods'][$this->_action['method']];
		
		//echo 'action: '.print_r($this->_action, 1)."\n";
		//echo 'method: '.print_r($this->_method, 1)."\n";
		
		if ('@' == $finded_action['method']{0})
		{
			$this->_controller = substr($finded_action['method'], 1);
			$this->_loadController($this->_controller);
		}	
	}
}
?>