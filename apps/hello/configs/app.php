<?php 
return array(
	// implementations
	'impls' => array(
		'cache'		=> 'FileCache_Impl',
		'url'		=> 'SimpleURL_Impl',
		'router'	=> 'MaskRouter_Impl',
		'runner'	=> 'Runner_Impl',
		'html-view'	=> 'TwigTemplater_Impl',
		'json-view'	=> 'JSONTemplater_Impl'
	),
	
	// scenarios for each type of request
	'scenarios' => array(
		'external'	=> 'External_Scenario',
		'internal'	=> 'Internal_Scenario'
	)	
);
?>
