<?php 
/**
 * Factory class
 */
class uF
{
    static $_instances;
    
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
            $class_name = substr($class_name, $pos + 1);
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
		
		$key = "create new [$class_name]";
    	uBenchmark::start($key);
		
        $object = null;
		
        switch (count($args))
        {
            case 0:
                $object = new $class_name();
            case 1:
                $object = new $class_name($args[0]);
            case 2:
                $object = new $class_name($args[0], $args[1]);
            case 3:
                $object = new $class_name($args[0], $args[1], $args[2]);
            case 4:
                $object = new $class_name($args[0], $args[1], $args[2], $args[3]);
            case 5:
                $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4]);
            case 6:
                $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
			case 7:
                $object = new $class_name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
        }
		
		uBenchmark::stop($key);
		return $object;
    }
    
}
?>
