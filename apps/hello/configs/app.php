<?php 
return array(
	// implementations
	'impls' => array(
		'url'		=> 'SimpleURL_Impl',
		'router'	=> 'MaskRouter_Impl',
		'runner'	=> 'Runner_Impl',
		'html-view'	=> 'SmartyTemplater_Impl',
		'json-view'	=> 'JSONTemplater_Impl'
	),
	
	// scenarios for each type of request
	'scenarios' => array(
		'external'	=> 'External_Scenario',
		'internal'	=> 'Internal_Scenario'
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
