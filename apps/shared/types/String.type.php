<?php 
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

?>
