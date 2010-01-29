<?php
class Any_Scenario 
{
	public $request;
	
	function __construct(&$request)
	{
		$this->request =& $request;
	}
}
?>