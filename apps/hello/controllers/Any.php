<?php
class cAny
{
	protected $_request;
	
	function __construct(&$request)
	{
		$this->_request =& $request;
	}
}
?>