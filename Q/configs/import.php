<?php
return array(
	'cache' => array(
		'file' => 'q:cache/import.txt',
		'enabled' => false
	),
	'scanner' => array(
		'directories' => array(
			'q:classes', 'q:impls'
		),
		'filenames'	=> '/\.(class|impl)\.php$/',
	)
);
?>