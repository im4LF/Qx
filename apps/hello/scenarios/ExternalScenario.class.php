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
		$impls = QF::s('Configs')->impls;
		$this->_request->url		= QF::n($impls['url'], $this->_request->raw_url)->parse();
		$this->_request->router		= QF::n($impls['router'], $this->_request)->route();
		$this->_request->runner		= QF::n($impls['runner'], $this->_request)->run();
		$this->_request->response	= $this->_request->runner->result();
		
		$requests = QF::s('RequestManager')->getRequests();
		
		$responses = array();
		foreach ($requests as $url => $request)
		{
			echo $url.': '.print_r($request->response, 1)."\n";
		}
		
		QF::n('Viewer', $this->_request)->view();
	}
}
?>