<?php
class URL 
{
	protected $_raw_url;
	protected $_properties;
	
	function __construct($url = null) 
	{
		$this->_raw_url = $url;
	}
	
	function __get($name) 
	{
		return $this->_properties[$name];
	}
	
	function parse() 
	{
		$impl = QF::s('Configs')->impls['url']; 
		$this->_properties = QF::n($impl, $this->_raw_url)->parse();
		return $this;
	}
}
?>