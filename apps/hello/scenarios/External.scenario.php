<?php
class External_Scenario
{
	protected $_request;
	
	function __construct(&$request)
	{
		$this->_request =& $request;
	}
	
	function run()
	{
		$b_key = 'main request - ['.$this->_request->raw_url.']';
		Benchmark::start($b_key);
		
		$impls = QF::s('Configs')->impls;
		$this->_request->url		= QF::n($impls['url'], $this->_request->raw_url)->parse();
		$this->_request->router		= QF::n($impls['router'], $this->_request)->route();
		$this->_request->runner		= QF::n($impls['runner'], $this->_request)->run();
		
		Benchmark::stop($b_key);
		
		// some test requests
		QF::n('Request', '/user.html', array('name' => 'user'))->dispatch();
		QF::n('Request', '/user/.login.html', array('name' => 'user_login', 'method'=>'POST'))->dispatch();
		
		$reg_data = array(
			'email' => 'tester@tester.tester',
			'password' => '123',
			'confirm_password' => '1234'			
		);
		QF::n('Request', '/user/.register.json', array(
			'name' => 'user_register',
			'method' => 'POST',
			'post'	 =>& $reg_data
		))->dispatch();
		
		// prepare data for view
		$responses = $this->_request->runner->result;						// main request
		foreach (QF::s('RequestManager')->requests as $name => $request)	// all others
		{
			if ('__main__' === $name)
				continue;

			$responses['__'.$name.'__'] = $request->response;
		}
		
		$responses['__debug__']['benchmarks'] = Benchmark::get();		// save debug information
		
		//echo print_r($responses, 1);
		//echo $this->_request->router->controller."\n";
		// detect view
		$controller_name = $this->_request->router->controller;
		$method_name = $this->_request->runner->action['method'];
		$views = QF::s('Configs')->controllers[$controller_name]['views'];
		
		$found = false;
		foreach ($views as $mask => $view)
		{
			if ($mask !== $method_name)
				continue;
			
			$found = true;
			break;
		}
		
		if (!$found)
			$view = $views['*'];
		
		echo QF::n($impls[$this->_request->url->view.'-view'], $view)	// create viewer
					->view($responses);									// and make view
	}
}
?>