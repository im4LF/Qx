<?php 
class Smarty_Templater_Impl
{
    public $smarty;
    
    function __construct()
    {
        $config = import::config('app:configs/smarty.php');
        import::from($config->lib);
		
        $this->smarty = new Smarty;
        $this->smarty->debugging = $config->debugging;
        $this->smarty->template_dir = import::buildPath($config->template_dir);
        $this->smarty->compile_dir = import::buildPath($config->compile_dir);
        $this->smarty->cache_dir = import::buildPath($config->cache_dir);
    }
    
    function view($data, $template)
    {
        foreach ($data as $k=>$v)
        {
            $this->smarty->assign($k, $v);
        }
        
		$result = '';
		ob_start();
        $this->smarty->display($template.'.tpl');
		$result = ob_get_contents();
		ob_clean();
		
		return $result;
    }
}
?>
