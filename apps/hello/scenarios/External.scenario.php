<?php
class External_Scenario extends Any_Scenario
{
	function run()
	{
		F::s('RequestManager')->addRequest($this->request);		// add new Request to RequestManager
		
		$b_key = 'main request - ['.$this->request->raw_url.']';
		Benchmark::start($b_key);
		
		$this->request->url    = F::n($this->_impls['url'], $this->request->raw_url)->parse();
		$this->request->router = F::n($this->_impls['router'], $this->request)->route();
		$this->request->runner = F::n($this->_impls['runner'], $this->request)->run();
		
		Benchmark::stop($b_key);
		
		return $this;
	}
	
	function done()
	{
		// prepare data for view
		$responses = $this->request->runner->result;						// main request
		foreach (F::s('RequestManager')->requests as $name => $request)	// all others
		{
			if ('__main__' === $name)
				continue;

			$responses['__'.$name.'__'] = $request->response;
		}
		
		$responses['__debug__']['benchmarks'] = Benchmark::get();		// save debug information
		
		// detect view
		$controller_name = $this->request->runner->controller_name;
		$method_name = $this->request->runner->action['method'];
		
		$views = F::s('Configs')->controllers[$controller_name]['views'];
		
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
		
		echo F::n($this->_impls[$this->request->url->view.'-view'])	// create viewer
					->view($responses, $view);						// and make view
	}
}
?>