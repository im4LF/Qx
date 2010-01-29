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

class String extends Any_Type
{
    function __construct($value)
    {
        parent::__construct($value);
        
        if ($this->_value !== null)
            settype($this->_value, 'string');
    }
    
    function required()
    {
        return (isset($this->_value) && strlen($this->_value) > 0);
    }
	
	function optional()
	{
		return true;
	}
    
    function min($length)
    {
        return (strlen($this->_value) >= (int) $length);
    }
    
    function eq($value)
    {
        return ($this->_value == (is_object($value) ? $value->valueOf() : (string) $value));
    }
    
    function neq($value)
    {
        return ($this->_value != (is_object($value) ? $value->valueOf() : (string) $value));
    }
    
}

class Email extends String
{
    function valid()
    {
        return true;
    }
	
	function exists()
	{
		return false;
	}
}