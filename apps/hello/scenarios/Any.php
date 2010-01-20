<?php
class sAny
{
	public $request;
	protected $_impls;
	
	function __construct(&$request)
	{
		$this->request =& $request;
	}
	
	function init()
	{
		$this->_impls = QF::s('Configs')->impls;
		return $this;
	}
	
	function done()	{}
}
?>