<?php 
return array(
	// implementations
	'impls' => array(
		'cache'		=> 'File_Cache_Impl',
		'url'		=> 'Simple_URL_Impl',
		'router'	=> 'Mask_Router_Impl',
		'runner'	=> 'Runner_Impl',
		'html-view'	=> 'Twig_Templater_Impl',
		'json-view'	=> 'JSON_Templater_Impl'
	),
	
	// scenarios for each type of request
	'scenarios' => array(
		'external'	=> 'External_Scenario',
		'internal'	=> 'Internal_Scenario'
	)	
);
?>
