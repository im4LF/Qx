<?php
return array(
	// scenarios
	'scenarios' => array(
		'application'	=> array(
			'class'	=> 'Application_Scenario',
			'impls'	=> array(
				'url'		=> 'Simple_URL_Impl',
				'router'	=> 'Mask_Router_Impl',
				'html'		=> 'Smarty_Templater_Impl',
				'json'		=> 'JSON_Templater_Impl'
			)
		),
	)
);
?>