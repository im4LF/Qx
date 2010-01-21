<?php
class Smarty_Templater_Impl
{
	protected $_smarty;
	protected $_template;
	
	function __construct($template)
	{
		$this->_template = $template.'.tpl';
		
		$config = import::config('app:smarty-templater.php');
		
		import::from($config['lib']);
		$this->_smarty = new Smarty;
		$this->_smarty->debugging = $config['debugging'];
		$this->_smarty->template_dir = import::buildPath($config['template_dir']);
		$this->_smarty->compile_dir = import::buildPath($config['compile_dir']);
		$this->_smarty->cache_dir = import::buildPath($config['cache_dir']);
	}
	
	function view($data)
	{
		foreach ($data as $k => $v)
		{
			$this->_smarty->assign($k, $v);
		}
		
		return $this->_smarty->fetch($this->_template);
	}
}
?>