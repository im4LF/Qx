<?php
class Any_Type
{
	protected $_value;
    
    function __construct($value)
    {
        $this->_value = $value;
    }
	
	function update($value)
	{
		$this->_value = $value;
	}
	
	function valueOf()
	{
		return $this->_value;
	}
}
?>