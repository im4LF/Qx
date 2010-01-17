<?php
class TwigTemplater_Impl
{
	protected $_twig;
	protected $_template;
	
	function __construct($template)
	{
		$this->_template = $template.'.html';
		
		$config = import::config('app:twig-templater.php');

		import::from($config['lib']);
		Twig_Autoloader::register();
		 
		$loader = new Twig_Loader_Filesystem(import::buildPath($config['template_dir']));
		$this->_twig = new Twig_Environment($loader, array(
			'debug' => $config['debugging'],
			'cache' => import::buildPath($config['compile_dir']),
		));
	}
	
	function view($data)
	{
		$template = $this->_twig->loadTemplate($this->_template);
		return $template->render($data);
	}
}
?>