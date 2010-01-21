<?php
class Any_Scenario
{
	public $request;
	protected $_impls;
	
	function __construct(&$request)
	{
		$this->request =& $request;
	}
	
	function init()
	{
		$this->_impls = F::s('Configs')->impls;
		return $this;
	}
	
	function done()	{}
}
?>