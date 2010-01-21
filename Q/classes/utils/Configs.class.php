<?php
class Configs 
{
	function __set($name, $value) 
	{
		$this->$name = $value;
	}
	
	function __get($name) 
	{
		if (!isset($this->$name)) 
			$this->$name = null;

		return $this->$name;
	}
}
?>