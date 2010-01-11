<?php
class Benchmark
{
	protected $_keys;
	
	function start($key)
	{
		$this->_keys[$key]['t0'] = microtime(true);
		
		return $this; 
	}
	
	function stop($key)
	{
		$this->_keys[$key]['t1'] = microtime(true);
		$this->_keys[$key]['dt'] = $this->_keys[$key]['t1'] - $this->_keys[$key]['t0'];
		
		return $this->_keys[$key]['dt'];
	}
	
	function dt($key = null)
	{
		if (!$key)
		{
			return $this->_keys;
		}
		
		return $this->_keys[$key]['dt'];
	}
}
?>