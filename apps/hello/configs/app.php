<?php
return array(
	// implementations
	'impls' => array(
		'url'		=> 'iURL_Simple',
		'router'	=> 'iRouter_Mask',
		'html-view'	=> 'iTemplater_Twig',
		'json-view'	=> 'iTemplater_JSON'
	),
	
	// scenarios
	'scenarios' => array(
		'external'	=> 'sExternal',
		'internal'	=> 'sInternal'
	),
	
	'db' => array(
		'dsn' => 'mysql://root:123456@localhost/savanna'
	),

);
?>