<?php
return array(
	// implementations
	'impls' => array(
		'url'		=> 'Simple_URL_Impl',
		'router'	=> 'Mask_Router_Impl',
		'html-view'	=> 'Twig_Templater_Impl',
		'json-view'	=> 'JSON_Templater_Impl'
	),
	
	// scenarios
	'scenarios' => array(
		'application'	=> 'Application_Scenario',
		'internal'		=> 'Internal_Scenario'
	),
);
?>