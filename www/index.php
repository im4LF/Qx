<?php
$t0 = microtime(true);

define('APP_PATH', realpath('../apps/hello'));			// define app path constants
define('SHARED_PATH', realpath('../apps/shared'));	// shared path constant
require '../Q/Q.php';							// require Q

//import::scan('app:import.php');	// scan defined paths in import configuration

F('tString', '');
F('tEmail', '');
F('tInteger', '');
F('xUser', '');
F('xUser_Register', '');
F('iURL_Simple', '');
F('iRouter_Mask', '');

/*F('Request',	// create new Request in factory		 
	'/', 		// request URI
	array(		
		'method' => 'POST',
		'name' => 'main',			// request name
		'scenario' => 'external',	// scenario name
		'cookie' =>& $_COOKIE,
		'get' =>& $_GET,
		'post' =>& $_POST,
		'files' =>& $_FILES 
	)
)->dispatch();	// run request dispatching*/

$t1 = microtime(true);
echo 'dt all : '.($t1-$t0)."\n";
echo 'memory: '. number_format(function_exists('memory_get_usage') ? (memory_get_usage() / 1024 / 1024) : 0, 2)."MB\n";
echo 'included_files: '.count(get_included_files())."\n";

//echo print_r(Benchmark::$marks, 1);
