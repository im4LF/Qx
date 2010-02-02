<?php
class Validator
{
	protected $_value;
	protected $_pointers;
	protected $_callback;	
	
	protected $_optional = false;
	protected $_valid = true;
	protected $_errors = array();
	
	function __construct($value)
	{
		$this->_value = $value;
	}
	
	function pointers(&$pointers)
	{
		$this->_pointers =& $pointers;
		return $this;
	}
	
	function callbacks(&$object)
	{
		$this->_callbacks =& $object;
		return $this;
	}
	
	function _as($alias)
	{
		return $this;
	}
	
	function valid()
	{
		return $this->_valid;
	}
	
	function errors()
	{
		return $this->_errors;
	}
	
	protected function _error($rule)
	{
		$this->_valid = false;
		$this->_rules[$rule] = true;
	}
	
	function rule($rule)
	{
		$alias = $rule;
		// if rule have alias
		if (preg_match_all('/(.+?)(\s+as\s+(\w+)|$)/i', $rule, $matches, PREG_SET_ORDER) && isset($matches[0][3]))
		{
			$rule = $matches[0][1];
			$alias = $matches[0][3];
		}
		 
		$args = array();		
		if (false !== ($spos = strpos($rule, '(')))	// if rule have arguments
		{		
			preg_match_all('/\((.*)\)/', $rule, $matches, PREG_SET_ORDER);	// separate rule name and rule arguments
			$buf = $matches[0][1];
			$rule = substr($rule, 0, $spos);
		 
			$code = '';			
			if (preg_match_all('/\$(\w+)/', $buf, $matches, PREG_SET_ORDER))	// if arguments have pointers
			{
				foreach ($matches as $match)
				{
					$pointer = $match[1];
					$code .= '$__param_'.$match[1].'=$this->_pointers[\''.$pointer.'\'];';
					$buf = str_replace($match[1], '__param_'.$match[1], $buf);
				}
			}
			$code .= '$args=array('.$buf.');';
			// build args
			eval($code);
		}
		
		echo "$rule AS $alias, args: ".print_r($args, 1);
		//$this->_addRule($rule, $args, $alias);
		 
		return $this;
	}
	
	function callRules($rule, $args, $alias)
	{
		
	} 
}

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
		if (0 === strlen($this->_value))
			$this->_error('required');
			
		return $this;
	}
	
	function optional() 
	{
		$this->_optional = true;
		return $this;
	}
	
	function min($length)
	{
		return $this;
	}
}

class Email extends Validator
{
	function valid()
	{
		if ($this->_optional && empty($this->_value))
			return $this;
		
		$re = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*';
    	$re.= '@([-a-z0-9]+\.)+([a-z]{2,3}';
    	$re.= '|info|arpa|aero|coop|name|museum)$/ix';
    	//return preg_match($re, $this->_value);
		
		return $this;
	}
}

$value = new String('test');
$value->rule('min(6) as min');
if (!$value->required()->min(6)->_as('min')->valid())
	print_r($value->errors());
