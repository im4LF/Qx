<?php
require('../Q/init.php');			// initialize Q framework

Q::app(array(
	'app'		=> '../apps/hello',	// directory with application
	'shared'	=> '../apps/shared'	// directory with shared libs and classes
))->run();							// create and run application
?>