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
			'app:scenarios', 'app:impls', 'app:controllers',
			'shared:types'
		),
		'filenames'	=> '/\.(scenario|controller|action|impl|type)\.php$/',
	)
);
?>