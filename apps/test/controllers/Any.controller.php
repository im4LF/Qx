<?php
class Any_Controller
{
	public $request;
	
	function __construct(&$request)
	{
		$this->request =& $request;
	}
}
?>