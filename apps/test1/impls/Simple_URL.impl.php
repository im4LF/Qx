<?php 
class Simple_URL_Impl extends QObject
{
	protected $raw_url;
	protected $path = '/';
	protected $action = 'index';
	protected $args = array();
	protected $viewtype = 'html';
	
	protected $__array_properties = array('args');
	
    function parse($raw_url)
    {
    	$this->raw_url = $raw_url;
        $buf = parse_url($raw_url);
        $this->path = $buf['path'];
        
        if (false !== ($start = strrpos($this->path, '.')))	// parse view
        {
            $this->viewtype = substr($this->path, $start + 1);
            $this->path = substr($this->path, 0, $start);
        }
        
        $params_re = '/\/-([\w\-]+)(\/([^\/]+))?/';
        $matches = array();
        if (preg_match_all($params_re, $this->path, $matches, PREG_SET_ORDER))	// extract url parameters
        {
            foreach ($matches as $param)
            {
                $this->args[$param[1]] = $param[3];
            }
            $this->path = preg_replace($params_re, '/', $this->path);
        }
        
        $action_re = '/\/\.([\w\-]+)/';
        if (preg_match_all($action_re, $this->path, $matches, PREG_SET_ORDER))	// extract action
        {
            $this->action = $matches[0][1];
            $this->path = preg_replace($action_re, '/', $this->path);
        }

        $this->path = preg_replace('/\/+/', '/', $this->path);
        $this->path .= $this->path{strlen($this->path) - 1} === '/' ? '' : '/';
        
        return $this;
    }
	
	function build()
	{
	}
}