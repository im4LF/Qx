<?php
return array(
	'cache' => array(
		'file' => 'app:cache/import.txt',
		'enabled' => false
	),
	'import' => array(
		'mask' => '/\.php$/'
	),
	'scanner' => array(
		'directories' => array(
			'shared:validators', 
			'app:impls', 'app:controllers', 'app:scenarios'
		),
		'filenames'	=> '/\.(class|controller|action|validator|impl|scenario)\.php$/',
	)
);
?>