<?php
return array(
	'controllers' => array(
		'impl' => 'File_Cache_Impl',
				
		'autoload' => true,
		'autosave' => true,
		
		'file' => 'app:cache/controllers.txt'
	),
	'queries' => array(
		'impl' => 'Memcache_Cache_Impl',
				
		'autoload' => false,
		'autosave' => false,		
	)
); 
?>