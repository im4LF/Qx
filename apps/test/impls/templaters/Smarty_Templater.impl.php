<?php 
class Smarty_Templater_Impl
{
    protected $_smarty;
    
    function __construct()
    {
        $config = import::config('app:configs/smarty.yml');
        import::from($config->lib);
		
        $this->_smarty = new Smarty;
        $this->_smarty->debugging = $config->debugging;
        $this->_smarty->template_dir = import::buildPath($config->template_dir);
        $this->_smarty->compile_dir = import::buildPath($config->compile_dir);
        $this->_smarty->cache_dir = import::buildPath($config->cache_dir);
    }
    
    function view($data, $template)
    {
        foreach ($data as $k=>$v)
        {
            $this->_smarty->assign($k, $v);
        }
        
        return $this->_smarty->fetch($template.'.tpl');
    }
}
?>
