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
		$this->_request->url = QF::n('URL', $this->_request->raw_url)->parse();
		$this->_request->router = QF::n('Router', $this->_request)->route();
		$this->_request->runner = QF::n('Runner', $this->_request)->run();
		$this->_request->response = $this->_request->runner->result();
	}
}
?>