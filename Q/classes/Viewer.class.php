<?php
class Viewer
{
	protected $_request;
	
	function __construct(&$request)
	{
		$this->_request =& $request;
	}
	
	function view()
	{
		echo 'current controller:'.$this->_request->router->controller;
	}
}
?>