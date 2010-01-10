<?php
/**
 * # add rules array and run validation
 * Validator::init(new String($value))->rules(array('required', 'min(6) as min'))->validate();
 * 
 * # add required rule, min rule and make alias for min
 * Validator::init(new Email($value))->required()->min(6)->_as('min')->validate();
 * 
 * # remove required rule and run validation again, 
 * # but validation process no rerun rules  
 * # call remove() without arguments mean remove all rules
 * Validator::init(new Email($value))->remove('required')->validate();
 * 
 * # for rerun validation you need to reset rule by name
 * Validator::init(new Email($value))->reset('valid')->validate();
 * 
 * # of pass array with names and aliases
 * Validator::init(new Email($value))->reset(array('valid', 'min'))->validate();
 * 
 * # of reset all
 * Validator::init(new Email($value))->reset()->validate();
 */
class Validator
{
	protected $_value;
	protected $_pointers;
	protected $_callback;
	
	protected $_rules;
	protected $_errors;
	
	function __construct($value, $pointers = array(), &$callback = null)
	{
		$this->_value = $value;
		$this->_pointers = $pointers;
		$this->_callback =& $callback;
		
		return $this;
	}
	
	static function init($value, $pointers = array(), &$callback = null)
	{
		return new Validator($value, $pointers, $callback);
	}
	
	function value()
	{
		return $this->_value;
	}
	
	function rule($rule)
	{
		$alias = $rule;
		// if rule have alias
		if (preg_match_all('/(.+?)(\s+as\s+(\w+)|$)/i', $rule, $matches, PREG_SET_ORDER) && isset($matches[0][3]))
		{
			$alias = $matches[0][3];
			$rule = $matches[0][1];
		}
		
		$args = array();
		// if rule have arguments
		if (false !== ($spos = strpos($rule, '(')))
		{
			// separate rule name and rule arguments
			preg_match_all('/\((.*)\)/', $rule, $matches, PREG_SET_ORDER);
			$buf = $matches[0][1];
			$rule = substr($rule, 0, $spos);
			
			// make code for build arguments array 
			$code = '';
			// if arguments have pointers
			if (preg_match_all('/\$(\w+)/', $buf, $matches, PREG_SET_ORDER))
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
		
		$this->_addRule($rule, $args, $alias);
		
		return $this;
	}
	
	function remove($rules = null)
	{
		if (!$rules)
		{
			$this->_rules = array();
		}
		elseif (!is_array($rules))
		{
			unset($this->_rules[$rules]);			
		}
		else
		{
			foreach ($rules as $alias)
				unset($this->_rules[$alias]);
		}
		
		return $this;
	}
	
	function rules($rules = null)
	{
		if (!$rules)
			return $this->_rules;
			
		foreach ($rules as $rule)
			$this->rule($rule);
			
		return $this;
	}
	
	protected function _addRule($name, $args, $alias)
	{
		$at = '_value';
		if ('call' == $name)
		{
			$at = '_callback';
			$name = $args[0];
			$args[0] = $this->_value;
		}
		
		$this->_rules[$alias] = array(
			'at' => $at,
			'name' => $name,
			'args' => $args
		);
	}
	
	function __call($name, $args)
	{
		$this->_addRule($name, $args, $name);
		
		return $this;
	}
	
	function _as($alias)
	{
		$rule = array_pop($this->_rules);
		$this->_rules[$alias] = $rule;
		
		return $this;
	}
	
	function validate()
	{
		foreach ($this->_rules as $alias => $params)	// foreach rules
		{
			if ($params['validated']) continue;	// if rule already validated
			
			$result = call_user_method_array($params['name'], $this->{$params['at']}, $params['args']);
			$this->_rules[$alias]['result'] = $result;
			$this->_rules[$alias]['validated'] = true;
			
			if (!$result)	// if result is error
				$this->_errors[$alias] = true;	// save error
		}

		return $this;	
	}
	
	function errors()
	{
		return $this->_errors;
	}

	function haveErrors()
	{
		return (bool) count($this->_errors);
	}

	function reset($rules)
	{
		if (!$rules)
		{
			foreach ($this->_rules as $alias => $params)
			{
				$this->_rules[$alias]['validated'] = false;
				$this->_errors[$alias] = array();
			}
		}
		elseif (!is_array($rules))
		{
			$this->_rules[$rules]['validated'] = false;
			unset($this->_errors[$rules]);
		}
		else
		{
			foreach ($rules as $alias)
			{
				$this->_rules[$alias]['validated'] = false;
				unset($this->_errors[$alias]);
			}
		}
		
		return $this;
	}
}
/*
class callbacks
{
	function check_email_unique(&$value, $tmp)
	{
		$value->update($tmp);
		return false;
	}
}

require("../../apps/shared/types/Any.type.php");
require("../../apps/shared/types/String.type.php");
require("../../apps/shared/types/Email.type.php");

$email = new Email('');
$callbacks = new callbacks; 

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
	
echo 'results: '.print_r($res, 1);

$res->remove(array('required', 'minlen'))->reset(array('valid', 'unique'))->validate();

echo 'results: '.print_r($res, 1);*/
?>