<?php
class Application_Scenario extends Any_Scenario
{
	public $impls;
	
	function open()
	{
		$this->impls = import::config('app:configs/app.php')->impls;
		return $this;
	}
	
	function run()
	{
		$b_key = 'main request - ['.$this->request->raw_url.']';
		Benchmark::start($b_key);
		
		// parse current raw_url and save result to request
		$this->request->url    = F($this->impls['url'], $this->request->raw_url)->parse();
		
		// route current url - find controller and method
		$this->request->router = F($this->impls['router'], $this->request)->route();
		echo "run: {$this->request->router->controller}::{$this->request->router->method}\n";
		
		// create controller object
		$controller = F($this->request->router->controller, $this->request);
		
		// run method of controller object
		$this->request->runner = F('Runner', $controller, $this->request->router->method)->run();
				
		Benchmark::stop($b_key);
		return $this;
	}
	
	function close()
	{
		// get response of query
		$response = $this->request->runner->result;
		
		// get view name
		$view = $this->request->router->view;		
		echo "view: {$view}\n";
		
		// save same debug information
		$response['__debug__']['benchmarks'] = Benchmark::get();
		
		// get view implementation of current viewtype
		$impl = $this->impls[$this->request->url->view.'-view'];
		
		// apply view to response date
		echo F($impl)->view($response, $view);					
	}
}
?>