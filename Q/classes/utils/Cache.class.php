<?php


class Cache
{
	protected $_impl;
	
	function __construct($impl)
	{
		$this->_impl = F::n($impl);
	}
}
?>