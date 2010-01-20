<?php
class sInternal extends sAny
{
	function run()
	{
		$b_key = 'request ['.$this->request->raw_url.']';
		uBenchmark::start($b_key);
		
		$impls = uQF::s('Configs')->impls;
		
		$this->request->url      = uQF::n($impls['url'], $this->request->raw_url)->parse();
		$this->request->router   = uQF::n($impls['router'], $this->request)->route();
		$this->request->runner   = uQF::n($impls['runner'], $this->request)->run();
		$this->request->response = $this->request->runner->result;
		
		Benchmark::stop($b_key);
	}
}
?>