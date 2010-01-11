<?php
return array(
	'cache' => array(
		'enabled' => true,
		'file' => 'app:cache/import.txt',
	),
	'scanner' => array(
		'directories' => array(
			'shared:types', 
			'app:impls', 'app:controllers', 'app:scenarios',
			'q:classes', 'q:impls'
		),
		'filenames'	=> '/\.(class|controller|action|type|impl)\.php$/',
	)
);
?>