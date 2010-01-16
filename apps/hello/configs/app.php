<?php 
return array(
	// implementations
	'impls' => array(
		'url'		=> 'SimpleURL',
		'router'	=> 'MaskRouter',
		'runner'	=> 'RunnerWithValidation',
		'html-view'	=> 'SmartyTemplater',
		'json-view'	=> 'JSONTemplater'
	),
	
	// scenarios for each type of request
	'scenarios' => array(
		'external'	=> 'ExternalScenario',
		'internal'	=> 'InternalScenario'
	),
	
	'templaters' => array(
		'SmartyTemplater' => array(
			'lib' => 'q:libs/templaters/Smarty-2.6.25/libs',
			'config' => array(
				'template_dir'	=> 'app:views/smarty',
				'compile_dir'	=> 'app:tmp/smarty_compile',
				'cache_dir'		=> 'app:cache/smarty_cache',
				'debugging'		=> false
			)
		),
		
		'TwigTemplater'	=> array(
			'lib' => 'q:libs/templaters/Twig',
			'config' => array(
				'template_dir'	=> 'app:views/twig',
			)
		)
	)	
);
?>
