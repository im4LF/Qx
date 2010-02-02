<?php
class Application_Scenario
{
	public $request;
	public $response;
	public $view;
	public $impls;

	function __construct(&$request, $impls)
	{
		$this->request =& $request;
		$this->impls = $impls;
	}
	
	function open()
	{
		return $this;
	}
	
	function run()
	{
		$b_key = 'request - ['.$this->request->raw_url.']';
		Benchmark::start($b_key);
		
		$router = F($this->impls['router']);	// create new router implementation
		if (false === $router->route($this->request->url, $this->request->http_method))
		{
			$router->controller = 'Errors';
			$router->method = 'notFound';
			$router->view = '404';
		}
			
		$controller = F($router->controller, $this->request);	// create controller object
		
		$this->response = F('Runner', $controller)->run($router->method);	// run method of controller object
		$this->view = $router->view;
			
		Benchmark::stop($b_key);
		return $this;
	}
	
	function close()
	{
		$this->response['__debug__']['benchmarks'] = Benchmark::get();	// save same debug information
		
		$viewer = $this->impls[$this->request->url->viewtype];	// get view implementation for current viewtype
		
		echo F($viewer)->view($this->response, $this->view);	// apply view to response date					
	}
}
