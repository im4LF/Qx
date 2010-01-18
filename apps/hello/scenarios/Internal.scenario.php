<?php
class Internal_Scenario extends Any_Scenario
{
	function run()
	{
		$b_key = 'request ['.$this->request->raw_url.']';
		Benchmark::start($b_key);
		
		$impls = QF::s('Configs')->impls;
		
		$this->request->url      = QF::n($impls['url'], $this->request->raw_url)->parse();
		$this->request->router   = QF::n($impls['router'], $this->request)->route();
		$this->request->runner   = QF::n($impls['runner'], $this->request)->run();
		$this->request->response = $this->request->runner->result;
		
		Benchmark::stop($b_key);
	}
}
?>