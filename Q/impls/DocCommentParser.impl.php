<?php 
class DocCommentParser
{
	protected $_reflection;
	protected $_config;
	
	function parse($class_name)
	{
		$this->_reflection = new ReflectionClass($class_name);
		$doc_comment = $this->_reflection->getDocComment();
		
		$this->_parseActions($doc_comment);
		$this->_parseViews($doc_comment);
		
		$methods = $this->_reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method)
		{
			if (!($doc_comment = $method->getDocComment())) continue;
			
			$method_name = $method->getName();
			
			$this->_parseActions($doc_comment, $method_name);
			$this->_parseMethodConfigs($doc_comment, $method_name);
			$this->_parseViews($doc_comment, $method_name);
			
			if (!preg_match_all('/@param\s+(\w+)\s+\$([\w]+)\s+(\w+)(\s+\[(.*?)\])?/', $doc_comment, $matches, PREG_SET_ORDER)) continue;
			
			foreach ($matches as $match)
			{
				$this->_config['methods'][$method_name]['params'][$match[2]] = array(
					'type' => $match[1],
					'from' => $match[3],
					'rules' => $this->_parseString($match[5])
				);
			}
		}
		
		return $this->_config;
	}
	
	protected function _parseActions($doc_comment, $method_name = '')
	{
		if (!preg_match_all('/@action\s+([\w\:\.\*]+)(\s+([\w\@]+))?(\s+\[(.*?)\])?/', $doc_comment, $matches, PREG_SET_ORDER)) return;
		
		foreach ($matches as $match)
        {
        	$regex = str_replace(':', '\:', $match[1]);
			$regex = str_replace('.', '\.', $regex);
			$regex = '/'.str_replace('*', '[\w\-]+', $regex).'/';
			
            $this->_config['actions'][$match[1]] = array(
				'action' => $match[1], 
				'regex'  => $regex,
				'method' => !empty($match[3]) ? $match[3] : $method_name, 
				'params' => $this->_parseParamsString($match[5])
			);
        }
	}
	
	protected function _parseViews($doc_comment, $method_name = '')
	{
		if (!preg_match_all('/@view(\s+([\w]+))?(\s+([\w\.\/\-]+))/', $doc_comment, $matches, PREG_SET_ORDER))
			return;
		
		foreach ($matches as $match)
		{
			if (empty($match[2]) && empty($method_name))
				$this->_config['views']['*'] = $match[4];
			else
				$this->_config['views'][$method_name ? $method_name : $match[2]] = $match[4];
		}
	}
	
	protected function _parseMethodConfigs($doc_comment, $method_name)
	{
		if (!preg_match_all('/@config\s+(\w+)\s+\[(.*?)\]/', $doc_comment, $matches, PREG_SET_ORDER)) 
			return;
		
		foreach ($matches as $match)
		{
			$this->_config['methods'][$method_name]['configs'][$match[1]] = $this->_parseParamsString($match[2]);
		}
	}
	
	protected function _parseRules()
	{
		// (.+?)(\s+AS\s+(\w+)$)?
	}
	
	protected function _parseString($string)
    {
        $res = array();
        if (!preg_match_all('/(.+?)(,\s+|$)/', $string, $matches, PREG_SET_ORDER))
            return $res;
            
        foreach ($matches as $match)
        {
        	/*$key = $match[1];
			$value = $match[1];
			if (preg_match_all('/(.+?)(\s+as\s+(\w+)|$)/i', $key, $matches1, PREG_SET_ORDER) && isset($matches1[0][3]))
			{
				$key = $matches1[0][3];
				$value = $matches1[0][1];
			}
            $res[$key] = $value;*/
			$res[$match[1]] = $match[1];
        }
        
        return $res;
    }
	
	protected function _parseParamsString($string)
	{
		$res = array();
        if (!preg_match_all('/(.+?)(,\s+|$)/', $string, $matches, PREG_SET_ORDER))
            return $res;
            
        foreach ($matches as $match)
        {
        	$key = $match[1];
			if (count($values = explode(' ', $match[1])))
			{
				$key = array_shift($values);
				$values = array_fill_keys($values, true);
			}
			$res[$key] = $values;
        }
        
        return $res;
	}
}
?>
