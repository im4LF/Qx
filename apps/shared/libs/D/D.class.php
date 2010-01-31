<?php
define('D_PATH', realpath(dirname(__FILE__)));

function DF($dsn)
{
	$config = @parse_url($dsn);
	if (isset($config['query']))
	{
		$buf = explode('&', $config['query']);
		foreach ($buf as $param)
		{
			list ($k, $v) = explode('=', $param);
			$config['params'][$k] = $v;
		}
	}
	
	$impl = ucfirst($config['scheme'].'_D');
	$filename = D_PATH.DIRECTORY_SEPARATOR.'impls'.DIRECTORY_SEPARATOR.$impl.'.impl.php';
	$classname = $impl.'_Impl';
	if (!file_exists($filename))
		throw new Exception('Implementation ['.$classname.'] not found');
	
	require $filename;
	
	$object = new $classname($config); 
	return $object;
}

class D
{
	protected $_config;
	protected $_ok;
	protected $_action;
	protected $_error;
	protected $_place_holders = array(
		'i' => 'integer',		
		'f' => 'float',
		'b' => 'boolean',
		's' => 'string',
		'd' => 'date',
		't' => 'time',
		'dt' => 'datetime',
	);
	
	function ok()
	{
		return $this->_ok;
	}
	
	function error()
	{
		return $this->_error;
	}
}
/*
$dsn = 'mysql://root:123456@localhost/kohana?charset=utf8';
$db = DF($dsn)->connect();

$value = '\"""12,3"';

$db->tablePrefix('kohana__');
$values = array(10, 20);
if (!$db->query("SELECT * FROM #pages AS P LEFT JOIN #pages AS P1 ON(P.id = P1.id) WHERE id = ?i OR id = ?i", $values)->ok())	
	echo print_r($db->error(), 1)."\n";

echo "num rows: ".$db->fetchMode('object')->numRows()."\n";
while ($row = $db->fetch())
{
	print_r($row);
}

$db->disconnect();*/
?>