<?php

class RequestManager 
{
	protected $_requests	= array();
	protected $_stack		= array();
	
	function pushRequest(&$request) 
	{
		$this->_requests[]		=& $request;	// add request
		array_push($this->_stack, $request);
				
		return $this;
	}
	
	function popRequest()
	{
		return array_pop($this->_stack);		
	}
	
	function hasExternal()
	{
		return (bool) count($this->_requests);
	}
}
?>