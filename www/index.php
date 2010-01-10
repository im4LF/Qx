<?php
$all_t0 = microtime(true);

$qinit_t0 = microtime(true);
require('../Q/init.php');			// initialize Q framework
$qinit_t1 = microtime(true);
echo "qinit dt: ".($qinit_t1 - $qinit_t0)."\n";

Q::app(array(
	'app'		=> '../apps/hello',	// directory with application
	'shared'	=> '../apps/shared'	// directory with shared libs and classes
))->run();							// create and run application

$all_t1 = microtime(true);
echo "all dt: ".($all_t1 - $all_t0)."\n";

$memory = function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0;

echo 'memory:'. number_format($memory, 2)."MB\n";
echo 'included_files:'.count(get_included_files());
?>