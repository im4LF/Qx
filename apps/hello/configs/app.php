<?php 
return array(
	// implementations
	'impls' => array(
		'cache'		=> 'iCache_File',
		'url'		=> 'iURL_Simple',
		'router'	=> 'iRouter_Mask',
		'runner'	=> 'iRunner',
		'html-view'	=> 'iTemplater_Twig',
		'json-view'	=> 'iTemplater_Json'
	),
	
	// scenarios for each type of request
	'scenarios' => array(
		'external'	=> 'sExternal',
		'internal'	=> 'sInternal'
	)	
);
?>
