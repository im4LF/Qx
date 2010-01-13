<?php
class Benchmark
{
	protected static $_marks;
	
	static function start($key)
	{
		self::$_marks[$key]->t0 = microtime(true);
		self::$_marks[$key]->memory = self::_memory();
		
		return $this; 
	}
	
	static function stop($key)
	{
		self::$_marks[$key]->t1 = microtime(true);
		self::$_marks[$key]->time = self::$_marks[$key]->t1 - self::$_marks[$key]->t0;
		
		return self::$_marks[$key];
	}
	
	static function get($key = null)
	{
		if (!$key)
		{
			return self::$_marks;
		}
		
		return self::$_marks[$key];
	}
	
	protected static function _memory()
	{
		static $func;

		if (null === $func)
		{
			$func = function_exists('memory_get_usage');
		}

		return $func ? memory_get_usage() : 0;
	}
}
?>