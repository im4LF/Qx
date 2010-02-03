<?php
class String extends Validator
{
	function __construct($value)
    {
        parent::__construct($value);
        
        if (null !== $this->_value)
            settype($this->_value, 'string');
    }
	
	function required() 
	{
		return $this->_addValidationResult('required', (0 !== strlen($this->_value)));
	}
	
	function optional() 
	{
		$this->_optional = true;
		return $this;
	}
	
	function eq($value)
	{
		if (is_object($value))
			$value = $value->value();
		
		return $this->_addValidationResult('eq', ($this->_value === $value));
	}
	
	function min($length)
	{
		return $this->_addValidationResult('min', strlen($this->_value) >= $length);
	}
}

class Text extends String
{}
?>