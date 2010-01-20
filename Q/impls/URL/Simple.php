<?php 
class iURL_Simple
{
    public $raw_url;
	public $path;
	public $action = 'default';
	public $state  = 'default';
	public $args   = array();
	public $view   = 'html';
    
    function __construct($url)
    {
        $this->raw_url	= $url;
    }
	
    function parse()
    {
        $buf = parse_url($this->raw_url);
        $this->path = $buf['path'];
        
        // parse view
        if (false !== ($start = strrpos($this->path, '.')))
        {
            $this->view = substr($this->path, $start + 1);
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
            if ($matches[0][3])
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
