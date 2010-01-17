<?php
$t0 = microtime(true);
require('../Q/init.php');			// initialize Q framework

Q::app(array(
	'app'		=> '../apps/hello',	// directory with application
	'shared'	=> '../apps/shared'	// directory with shared libs and classes
))->run();							// create and run application

$t1 = microtime(true);
echo 'dt all (index.php): '.($t1-$t0)."\n";
$memory = function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0;
echo 'memory: '. number_format($memory, 2)."MB\n";
echo 'included_files: '.count(get_included_files())."\n";
?>