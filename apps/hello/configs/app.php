<?php
return array(
	'impls' => array(
		'cache'		=> 'File_Cache_Impl',
		'url'		=> 'Simple_URL_Impl',
		'router'	=> 'Mask_Router_Impl',
		'runner'	=> 'Runner_Impl',
		'html-view'	=> 'Twig_Templater_Impl',
		'json-view'	=> 'JSON_Templater_Impl'
	),
	
	'scenarios' => array(
		'external'	=> 'External_Scenario',
		'internal'	=> 'Internal_Scenario'
	),
	
	'caches' => array(
		'controllers' => array(
			'impl' => 'File_Cache_Impl',
					
			'autoload' => true,
			'autosave' => true,
			
			'file' => 'app:cache/controllers.txt'
		),
		'queries' => array(
			'impl' => 'Memcache_Cache_Impl',
					
			'autoload' => false,
			'autosave' => false,		
		)
	)
);
?>