<?php
class ExternalScenario
{
	protected $_request;
	
	function __construct(&$request)
	{
		$this->_request =& $request;
	}
	
	function run()
	{
		$url_t0 = microtime(true);		
		$this->_request->url = QF::n('URL', $this->_request->raw_url)->parse();
		
		$url_t1 = microtime(true);
		echo "url dt: ".($url_t1 - $url_t0)."\n";
		
		$router_t0 = microtime(true);		
		$this->_request->router = QF::n('Router', $this->_request)->route();
		
		$router_t1 = microtime(true);
		echo "router dt: ".($router_t1 - $router_t0)."\n";
		
		$runner_t0 = microtime(true);
		$this->_request->response = QF::n('Runner', $this->_request)->run();
		
		$runner_t1 = microtime(true);
		echo "runner dt: ".($runner_t1 - $runner_t0)."\n";
	}
}
?>