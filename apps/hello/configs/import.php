<?php
return array(
	'ext' => '.php',
	'scanner' => array(
		'classes' => array('q:classes', 'app:classes'),
		'i' => array('q:impls', 'app:impls'),	// implementations
		's' => 'app:scenarios',					// scenarios
		'x' => 'app:controllers',				// controllers
		't' => 'shared:types',					// types
		'm' => 'app:models',					// models
		'u' => array('q:utils', 'app:utils')	// utils
	)
);
?>