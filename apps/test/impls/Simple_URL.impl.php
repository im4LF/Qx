<?php 
class Simple_URL_Impl
{
	protected $_raw_url;
	public $path;
	public $action;
	public $state;
	public $args;
	public $viewtype;
	
	function __construct()
	{
		$this->path = '/';
		$this->action = $this->state = 'default';
		$this->args = array();
		$this->viewtype = 'html';
	}
	
    function parse($raw_url)
    {
    	$this->_raw_url = $raw_url;
        $buf = parse_url($raw_url);
        $this->path = $buf['path'];
        
        // parse view
        if (false !== ($start = strrpos($this->path, '.')))
        {
            $this->viewtype = substr($this->path, $start + 1);
            $this->path = substr($this->path, 0, $start);
        }
        
        // try to parse url parameters
        $params_re = '/\/-([\w\-]+)(\/([^\/]+))?/';
        $matches = array();
        if (preg_match_all($params_re, $this->path, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as $param)
            {
                $this->args[$param[1]] = $param[3];
            }
            $this->path = preg_replace($params_re, '', $this->path);
        }
        
        // try to parse action and state
        $action_state_re = '/\/\.([\w\-]+)(\.([\w\-]+))?/';
        if (preg_match_all($action_state_re, $this->path, $matches, PREG_SET_ORDER))
        {
            $this->action = $matches[0][1];
            if (isset($matches[0][3]))
                $this->state = $matches[0][3];
                
            $this->path = preg_replace($action_state_re, '', $this->path);
        }
        
        $this->path = preg_replace('/\/+/', '/', $this->path);
        $this->path .= $this->path {strlen($this->path) - 1} == '/' ? '' : '/';
        
        return $this;
    }
	
	function build()
	{
	}
}
?>
