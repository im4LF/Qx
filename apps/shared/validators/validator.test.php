<?php
class Validator
{
	protected $_value;
	protected $_pointers;
	protected $_callbacks;	
	
	protected $_optional = false;
	protected $_valid = true;
	protected $_rules = array();
	protected $_errors = array();
	
	function __construct($value)
	{
		$this->_value = $value;
	}
	
	/**
	 * Return scalar value
	 * 
	 * @return scalar value 
	 */
	function value()
	{
		return $this->_value;
	}
	
	/**
	 * Set pointers for validation
	 * 
	 * @param array $pointers
	 * @return this 
	 */
	function pointers(&$pointers)
	{
		$this->_pointers =& $pointers;
		return $this;
	}
	
	/**
	 * Set object with callbacks functions
	 * 
	 * @param object $object
	 * @return this 
	 */
	function callbacks(&$object)
	{
		$this->_callbacks =& $object;
		return $this;
	}
	
	/**
	 * Set alias for rule
	 * 
	 * @param string $alias
	 * @return this 
	 */
	function alias($alias)
	{
		$result = array_pop($this->_rules);
		$this->_rules[$alias] = $result;
 
		return $this;
	}
	
	/**
	 * Is current value valid
	 * 
	 * @return bool 
	 */
	function valid()
	{
		return $this->_valid;
	}
	
	/**
	 * Return list of errors
	 * 
	 * @return array
	 */
	function errors()
	{
		return $this->_errors;
	}
	
	/**
	 * Add rule by string, parse and call method
	 * 
	 * @param string $rule
	 * @return this
	 */
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
		
		return $this->_callRule($rule, $args, $alias);
	}
	
	/**
	 * Add rules by array, each item is one rule
	 * 
	 * @param array $rules
	 * @return this
	 */
	function rules($rules)
	{
		foreach ($rules as $rule)
			$this->rule($rule);
		 
		return $this;
	}
	
	/**
	 * Call rule witch passed as string
	 * 
	 * @param string $rule
	 * @param array $args
	 * @param string $alias
	 * @return this
	 */
	protected function _callRule($rule, $args, $alias)
	{
		if ('call' === $rule)
		{
			$rule = $args[0];
			$args[0] = $this;
			$this->_addValidationResult($rule, call_user_method_array($rule, $this->_callbacks, $args));
		}
		else		
			call_user_method_array($rule, $this, $args);
		
		return $this->alias($alias);
	} 
	
	protected function _addValidationResult($rule, $result)
	{
		$this->_rules[$rule] = $result;
		if (false === $result) 
		{
			$this->_valid = false;
			$this->_errors[] = $rule;
		}
			
		return $this;
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

class Email extends String
{
	function correct()
	{
		if ($this->_optional && empty($this->_value))
			return $this->_addValidationResult('correct', true);
		
		$re = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*';
    	$re.= '@([-a-z0-9]+\.)+([a-z]{2,3}';
    	$re.= '|info|arpa|aero|coop|name|museum)$/ix';

		return $this->_addValidationResult('correct', (bool) preg_match($re, $this->_value));
	}
}

class callbacks
{
	function check_email_unique(&$value, $tmp)
	{
		return true;
	}
}

$callbacks = new callbacks;
$pointers = array('confirm_email' => new Email('test'), 'tmp' => 'zxc');

$value = new Email('testtest');
$value->callbacks($callbacks);
$value->pointers($pointers);

/*$value->rules(array(
	'required',
	'min(6) as minlen',
	'correct',
	'call("check_email_unique", $tmp) as unique',
	'eq($confirm_email) as eq__confirm_email'
	))->valid();*/

if (!$value->required()->min(6)->alias('min')->correct()->eq('test')->valid())
	print_r($value->errors());
	
//print_r($value);	
	
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
