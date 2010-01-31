<?php
class Twig_Templater_Impl
{
	protected $_twig;
	
	function __construct()
	{	
		import::unregister();	
		$config = import::config('app:configs/twig.php');

		import::from($config->lib);
		Twig_Autoloader::register();
		 
		$loader = new Twig_Loader_Filesystem(import::buildPath($config->template_dir));
		$this->_twig = new Twig_Environment($loader, array(
			'debug' => $config->debugging,
			'cache' => import::buildPath($config->compile_dir),
		));
	}
	
	function view($data, $template_name)
	{
		$template = $this->_twig->loadTemplate($template_name.'.html');
		return $template->render($data);
	}
}
?>