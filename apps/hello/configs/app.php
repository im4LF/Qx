<?php 
return array(
	// implementations
	'impls' => array(
		'url'		=> 'SimpleURL',
		'router'	=> 'MaskRouter',
		'parser'	=> 'DocCommentParser'
	),
	
	// scenarios for each type of request
	'scenarios' => array(
		'external'	=> 'ExternalScenario',
		'internal'	=> 'InternalScenario'
	),
	
	// templaters
	'templaters' => array(
		'html' => array(
			'templater' => 'SmartyTemplater'
		),
		'json' => array(
			'templater' => 'JsonTemplater'
		)
	)
);
?>
