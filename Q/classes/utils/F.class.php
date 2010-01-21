<?php 
/**
 * Factory class
 */
class F
{
    protected static $_instances;
    
	/**
	 * Registry emulation
	 * 
	 * @example F::r('ClassName:my-alias', 'param1', ...); - initialize and return registry class "ClassName" with "my-alias" alias
	 * @example F::r('ClassName:my-alias'); - return object of "ClassName" with registry key "my-alias" 
	 * 
	 * @return object
	 */
	static function r()
	{
		$args = func_get_args();
		$class_key = $args[0];
		$args[0] = substr($class_key, 0, strpos($class_key, ':'));
		if (!isset(self::$_instances[$class_key]))
        {
            $self = self;
            self::$_instances[$class_key] = call_user_method_array('n', $self, $args);
        }
        return self::$_instances[$class_key];
	}
	
    /**
     * Generate new singleton object of passed class name
     *
     * @example F::s('ClassName', 'param1', 'param2', ...); - initialize and return singleton object
     * @example F::s('ClassName'); - return singleton object
     * 
     * @return object
     */
    static function s()
    {
        $args = func_get_args();
        $class_name = $args[0];
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
     * @example F::n('ClassName', 'param1', 'param2', ...); - initialize and return new object
     * 
     * @return object
     */
    static function n()
    {
        $args = func_get_args();
        $class_name = array_shift($args);
        
        $key = "create new [$class_name]";
        Benchmark::start($key);
        
        $object = null;
        switch (count($args))
        {
            case 0:
                $object = new $class_name(); break;
            case 1:
                $object = new $class_name($args[0]); break;
            case 2:
                $object = new $class_name($args[0], $args[1]); break;
            case 3:
                $object = new $class_name($args[0], $args[1], $args[2]); break;
            case 4:
                $object = new $class_name($args[0], $args[1], $args[2], $args[3]); break;
            case 5:
                $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4]); break;
            case 6:
                $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]); break;
            case 7:
                $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]); break;
        }
		
		/*$reflection = new ReflectionClass($class_name);
		if (count($args))
			$object = $reflection->newInstanceArgs($args);
 		else
			$object = $reflection->newInstance();*/
        
        Benchmark::stop($key);
        return $object;
    }
    
}
?>
