<?php
class Any_Controller
{
	public $__x = 'index:default';
	
	public $request;
	public $response;
	
	function __construct(&$request)
	{
		$this->request =& $request;
	}
}
?>