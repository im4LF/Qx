<?php 
class SimpleURL_Impl
{
    protected $_raw_url;
	protected $_path;
	protected $_action;
	protected $_state;
	protected $_args;
	protected $_view;
    
    function __construct($url)
    {
        $this->_raw_url	= $url;
        $this->_action	= 'default';
        $this->_state	= 'default';
        $this->_args	= array();
        $this->_view	= 'html';
    }
	
	function __get($name) 
	{
		return $this->{'_'.$name};
	}
    
    function parse()
    {
        $buf = parse_url($this->_raw_url);
        $this->_path = $buf['path'];
        
        // parse view
        if (($start = strrpos($this->_path, '.')) !== false)
        {
            $this->_view = substr($this->_path, $start + 1);
            $this->_path = substr($this->_path, 0, $start);
        }
        
        // try to parse url parameters
        $params_re = '/\/-([\w\-]+)(\/([^\/]+))?/';
        $matches = array();
        if (preg_match_all($params_re, $this->_path, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as $param)
            {
                $this->_args[$param[1]] = $param[3];
            }
            $this->_path = preg_replace($params_re, '', $this->_path);
        }
        
        // try to parse action and state
        $action_state_re = '/\/\.([\w\-]+)(\.([\w\-]+))?/';
        if (preg_match_all($action_state_re, $this->_path, $matches, PREG_SET_ORDER))
        {
            $this->_action = $matches[0][1];
            if ($matches[0][3])
                $this->_state = $matches[0][3];
                
            $this->_path = preg_replace($action_state_re, '', $this->_path);
        }
        
        $this->_path = preg_replace('/\/+/', '/', $this->_path);
        $this->_path .= $this->_path {strlen($this->_path) - 1} == '/' ? '' : '/';
        
        return $this;
    }
}
?>
