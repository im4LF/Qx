<?php
/**
 * Q factory class
 */
class QF 
{
	protected static $_instances; 

	/**
	 * Generate new singleton object of passed class name
	 * 
	 * @return object
	 */
	static function s() 
	{
		$args = func_get_args();
		$class_name = $args[0];
		if (false !== ($pos = strpos($class_name, ':'))) 
		{
			$class_name = substr($class_name, $pos+1);
		}
		if (!isset(self::$_instances[$class_name])) 
		{
			$self = self;
			self::$_instances[$class_name] = call_user_method_array('n', $self, $args);
		}
		return self::$_instances[$class_name];
	}
	
	/**
	 * Generate new object of passed class name with arguments
	 * 
	 * @return object
	 */
	static function n() 
	{
		$args = func_get_args();
		$class_name = array_shift($args);
		
		$reflection = new ReflectionClass($class_name);
		if (count($args))
			return $reflection->newInstanceArgs($args);
		
		return $reflection->newInstance();
	} 
		
}
?>