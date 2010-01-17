<?php
return array(
	'cache' => array(
		'file' => 'app:cache/import.txt',
		'enabled' => false
	),
	
	'scanner' => array(
		'scenarios' => array('app:scenarios'),
		'impls' => array('q:impls', 'app:impls'),
		'classes' => array('q:classes', 'app:classes', 'shared:types'),
		'controllers' => array('app:controllers')
	)
);
?>