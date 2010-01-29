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
		'external'	=> 'Application_Scenario',
		'internal'	=> 'Internal_Scenario'
	),
	
	'db' => array(
		'dsn' => 'mysql://root:123456@localhost/savanna',
		//'dsn' => 'driver=mysql&host=localhost&username=root&password=123456&database=savanna&charset=utf8'
	),

);
?>