<?php 
// import configuration
return array(
	'cache' => array(
		'file' => 'q:cache/import.txt',
		'enabled' => true
	),
	'scanner' => array(
		// directories where classes will be searched
		'directories'	=> array('q:classes', 'q:impls'),
		
		// filenames where defined classes
		'filenames'		=> '/\.(class|impl)\.php$/'
	)	
);
?>
