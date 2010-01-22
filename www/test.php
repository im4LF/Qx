<?php

// \/\*\*(.+?)\*\/(\s+public)?\s+function\s+([\w]+)

/**
 * ABC
 */
class A
{
	function test0() {}

	/**
	 * 
	 * @param Email $email
	 * @param String $password
	 * @return 
	 */	
	function test($email, $password) {}
}

$r = new ReflectionClass('A');
//echo $r->getDocComment();
$filename = $r->getFileName();
$buf = unserialize(@file_get_contents('cache.txt'));

if (!$buf || $buf['key'] !== filemtime($filename)) 
{
	echo "write to cache\n";
	$buf = array(
		'value' => $r->getDocComment(),
		'key' => filemtime($filename)
	);

	file_put_contents('cache.txt', serialize($buf));
}
else
{
	echo "read from cache\n";
}

print_r($buf);

//$r = new ReflectionMethod('A', 'test');
//echo $r->getDocComment();
?>