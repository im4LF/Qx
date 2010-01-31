<?php
class Mysql_D_Impl extends D
{
	protected $_link;
	protected $_result;
	protected $_fetch_mode;
	protected $_fetch_function;
	protected $_table_prefix;
	
	protected function _formatValue($value, $type)
	{
		switch ($type)
		{
			case 'i': 
				return (int) $value;
			case 'f':
				return (float) str_replace(',', '.', $value);
			case 'b':
				return $value ? 1 : 0;
			case 'd':
				return date('\'Y-m-d\'', $value);
			case 't':
				return date('\'H:i:s\'', $value);
			case 'dt':
				return date('\'Y-m-d H:i:s\'', $value);
			default:
				return '\''.mysql_real_escape_string($value, $this->_link).'\'';
		}
	}
	
	function __construct($config)
	{
		$this->_ok = true;
		$this->_config = $config;
		$this->_config['path'] = substr($this->_config['path'], 1);
		$this->fetchMode('assoc');
	}
	
	protected function _setError()
	{
		$this->_ok = false;
		if (is_resource($this->_link))
		{
			$code = mysql_errno($this->_link);
			$message = mysql_error($this->_link);
		}
		else
		{
			$code = mysql_errno();
			$message = mysql_error();
		}
		$this->_error = (object) array(
			'code' => $code,
			'message' => $message,
			'action' => $this->_action,
			//'stack' => debug_backtrace(false)
		);
		return $this;
	}
	
	function connect()
	{
		$this->_action = 'connect';
		if (false === ($this->_link = @mysql_connect($this->_config['host'], $this->_config['user'], $this->_config['pass'])))
			return $this->_setError();
		
		$this->_action = 'select_db';	
		if (false === @mysql_select_db($this->_config['path'], $this->_link))
			return $this->_setError();
			
		if (isset($this->_config['params']['charset'])) 
		{
			$ok = false;
			if (function_exists('mysql_set_charset'))	// MySQL 5.0.7 and PHP 5.2.3 
				$ok = @mysql_set_charset($this->_config['params']['charset'], $this->_link);
			else
			{
				$ok = @mysql_query('SET NAMES "'.$this->_config['params']['charset'].'"', $this->_link);
				if (!$ok)
					return $this->_setError();
			}
		}
			
		return $this;
	}
	
	function disconnect()
	{
		$this->_action = 'mysql_close';
		if (false === mysql_close($this->_link))
			return $this->_setError();
			
		return $this;
	}
	
	function fetchMode($mode = null)
	{
		if (!$mode)
			return $this->_fetch_mode;
			
		$this->_fetch_mode = $mode;
		$this->_fetch_function = 'mysql_fetch_'.$mode;
		return $this;
	}
	
	function tablePrefix($prefix = null)
	{
		if (!$prefix)
			return $this->_table_prefix;
			
		$this->_table_prefix = (array) $prefix;
		return $this;
	}
	
	function numRows()
	{
		return mysql_num_rows($this->_result);
	}
	
	function affectedRows()
	{
		return mysql_affected_rows($this->_link);
	}
	
	function fetch()
	{
		$function = $this->_fetch_function;
		return $function($this->_result);
	}
	
	function query($sql, $values)
	{
		if (false === ($sql = $this->_buildQuery($sql, $values)))
			return $this->_setError();
			
		$this->_action = 'execute query: '.$sql;
		if (false === $this->_result = @mysql_query($sql, $this->_link))
			return $this->_setError();
			
		return $this;
	}
	
	protected function _buildQuery($sql, $values)
	{
		if (false !== strpos($sql, '#'))	// replace # by table prefix
		{
			$this->_action = 'build query, table prefixes: '.$sql;
			
			$buf = explode('#',$sql);
			for ($i = 1; $i < count($buf); $i++)
			{
				$prefix_id = 0;
				$prefix_id_len = 0;
				if (preg_match('/^(\d+)/', $buf[$i], $matches))
				{
					$prefix_id = $matches[1];
					$prefix_id_len = strlen($matches[1]);
				}
					
				if (!isset($this->_table_prefix[$prefix_id]))
					return false;
					
				$buf[$i] = $this->_table_prefix[$prefix_id].substr($buf[$i], $prefix_id_len);
			}
			$sql = implode('', $buf);
		}
		
		if (false !== strpos($sql, '?'))	// replace place holders by values
		{
			$buf = explode('?', $sql);
			$matches = array();
			for ($i = 1; $i < count($buf); $i++)
			{
				if (!preg_match('/^(\w+)/', $buf[$i], $matches))
					continue;
					
				$buf[$i] = $this->_formatValue($values[$i-1], $matches[1]).substr($buf[$i], strlen($matches[1]));
			}
			$sql = implode('', $buf);
		}
		return $sql;
	}
}
?>