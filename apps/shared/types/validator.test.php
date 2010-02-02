<?php
class Validator
{
	protected $_value;
	protected $_pointers;
	protected $_callbacks;	
	
	protected $_optional = false;
	protected $_valid = true;
	protected $_results = array();
	
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
		$result = array_pop($this->_results);
		$this->_results[$alias] = $result;
 
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
	
	function rule($rule)
	{
		$alias = $rule;		
		if (preg_match_all('/(.+?)(\s+as\s+(\w+)|$)/i', $rule, $matches, PREG_SET_ORDER) && isset($matches[0][3]))	// if rule have alias
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
			eval($code);	// build args
		}
		
		$this->_callRule($rule, $args, $alias);
		 
		return $this;
	}
	
	function rules($rules)
	{
		foreach ($rules as $rule)
			$this->rule($rule);
		 
		return $this;
	}
	
	protected function _callRule($rule, $args, $alias)
	{
		if ('call' === $rule)
		{
			$rule = $args[0];
			$args[0] = $this;
			$this->_addResult($rule, call_user_method_array($rule, $this->_callbacks, $args));
		}
		else
			call_user_method_array($rule, $this, $args);
			
		$this->_as($alias);
	} 
	
	protected function _addResult($rule, $result)
	{
		$this->_results[$rule] = $result;
		if (false === $result)
			$this->_valid = false;
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
		$this->_addResult('required', (0 === strlen($this->_value)));
			
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

class callbacks
{
	function check_email_unique(&$value, $tmp)
	{
		print_r($tmp);
		return false;
	}
}

$callbacks = new callbacks;
$pointers = array('confirm_email' => new Email('asd@asd.qwe'), 'tmp' => 'zxc');

$value = new String('test');
$value->callbacks($callbacks);
$value->pointers($pointers);
$value->rules(array(
	'required',
	'min(6) as minlen',
	'call("check_email_unique", $tmp) as unique',
	'eq($confirm_email) as eq__confirm_email'
	));

if (!$value->required()->min(6)->_as('min')->valid())
	print_r($value->errors());
	
print_r($value);	
	
/*
$res = Validator::init($email, array('confirm_email' => new Email('asd@asd.qwe'), 'tmp' => 'zxc'), $callbacks)
	->rules(array(
	'required',
	'min(6) as minlen',
	'call("check_email_unique", 123) as unique',
	'eq($confirm_email, $tmp) as eq__confirm_email'
	))
->validate();
	
echo 'results: '.print_r($res, 1);

$res = Validator::init($email, null, $callbacks)
	->required()
	->valid()
	->min(6)->_as('minlen')
	->call('check_email_unique', 'test')->_as('unique')
->validate();
*/	
