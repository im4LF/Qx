<?php
class Any_Controller
{
	protected $_request;
	
	function __construct(&$request)
	{
		$this->_request =& $request;
	}
}
?>