<?php
return array(
	'ext' => '.php',
	'scanner' => array(
		'i' => array('q:impls', 'app:impls'),	// implementations
		's' => 'app:scenarios',					// scenarios
		'c' => 'app:controllers',				// controllers
		't' => 'shared:types',					// types
		'm' => 'app:models',					// models
		'u' => array('q:utils', 'app:utils')
	)
);
?>