<?php
class sExternal extends sAny
{
	function run()
	{
		QF::s('RequestManager')->addRequest($this->request);		// add new Request to RequestManager
		
		$b_key = 'main request - ['.$this->request->raw_url.']';
		Benchmark::start($b_key);
		
		$this->request->url    = QF::n($this->_impls['url'], $this->request->raw_url)->parse();
		$this->request->router = QF::n($this->_impls['router'], $this->request)->route();
		$this->request->runner = QF::n($this->_impls['runner'], $this->request)->run();
		
		Benchmark::stop($b_key);
		
		return $this;
	}
	
	function done()
	{
		// prepare data for view
		$responses = $this->request->runner->result;						// main request
		foreach (QF::s('RequestManager')->requests as $name => $request)	// all others
		{
			if ('__main__' === $name)
				continue;

			$responses['__'.$name.'__'] = $request->response;
		}
		
		$responses['__debug__']['benchmarks'] = Benchmark::get();		// save debug information
		
		// detect view
		$controller_name = $this->request->runner->controller_name;
		$method_name = $this->request->runner->action['method'];
		
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
		
		echo QF::n($this->_impls[$this->request->url->view.'-view'])	// create viewer
					->view($responses, $view);							// and make view
	}
}
?>