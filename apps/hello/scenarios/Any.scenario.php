<?php
class Any_Scenario
{
	public $request;
	public $impls;
	
	function __construct(&$request)
	{
		$this->request =& $request;
	}
	
	function init()
	{
		$this->impls = F::s('Configs')->impls;
		return $this;
	}
	
	function done()	{}
}
?>