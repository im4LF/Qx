<?php

class RequestManager 
{
	protected $_requests;
	
	function addRequest(&$request) 
	{
		$this->_requests[$request->name] =& $request;
				
		return $this;
	}
	
	function __get($name)
	{
		return $this->{'_'.$name};
	}
}
?>