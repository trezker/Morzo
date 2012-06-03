<?php

error_reporting(~0);

$protocol =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http';
$host = $_SERVER['SERVER_NAME'];
$port = $_SERVER['SERVER_PORT'];
if(($protocol == 'http' && $port != 80) || ($protocol == 'https' && $port != 443)) {
	$port = ':'.$port;
} else {
	$port = '';
}
$base_url = "$protocol://$host$port/";
$GLOBALS['base_url'] = $base_url;

require_once '../util/htmltemplate.php';
require_once '../util/log.php';

$argv = explode("/",$_SERVER['PATH_INFO']);

$memcache;
$memcache = new Memcache;	
$memcache->addServer('127.0.0.1', 11211);
# Gets key / value pair from memcache
function Get_cache($key) {
	global $memcache;
	return ($memcache) ? $memcache->get($key) : false;
}

# Puts key / value pair into memcache
function Set_cache($key, $object, $timeout = 60) {
	global $memcache;
	return ($memcache) ? $memcache->set($key, $object, MEMCACHE_COMPRESSED, $timeout) : NULL;
}

function Delete_cache($key) {
	global $memcache;
	return ($memcache) ? $memcache->delete($key) : NULL;
}

if($argv[1] == "info")
{
	phpinfo();
}
else if($argv[1] == "mem")
{
	
	Set_cache('test', 'woop');
	var_dump(Get_cache('test'));
	var_dump(Get_cache('tes'));
}
else if($argv[1] == "debug")
{
	//
	for ($i = 2; $i<count($argv); $i++)
	{
		$argv[$i-1] = $argv[$i];
	}
	unset($argv[count($argv)-1]);
?>
	<html>
		<head>
			<link rel="stylesheet" type="text/css" media="screen" href="css/style.php">
		</head>
		<body>
			Hello
			<?php
			echo "In: " . $_SERVER['PATH_INFO'] . "<br />";

			echo "Arguments: " . count($argv) . "<br />";
			var_dump($argv);
			foreach ($argv as $arg)
			{
				echo "Arg: " . $arg . "<br />";
			}
			?>
		</body>
	</html>
<?php
}
else
{
	//Load requested controller
	if(count($argv)>1)
	{
		$controller_path = '../controllers/'.$argv[1].'.php';
		if(!file_exists($controller_path))
		{
			Log_message("Could not load controller: ".$controller_path);
			header("HTTP/1.0 404 Not Found");
			include '../blocked.php';
		}
		else
		{
			$r = include($controller_path);
	//		echo 'Result: '. $r .' : ' . $controller_path . '<br />';
			if ($r != 1) {
				//Todo: Log error
	//			echo 'Failed '.$r.': ' . $controller_path . '<br />';
				header("HTTP/1.0 404 Not Found");
				include '../blocked.php';
			}
			else
			{
				$obj = new $argv[1];

				if(count($argv)<3)
				{
					session_start();
					$obj->Index();
				}
				else if(!method_exists($obj, $argv[2]))
				{
					header("HTTP/1.0 404 Not Found");
					include '../blocked.php';
				}
				else
				{
					session_start();
					$funcargs = array_slice($argv, 3);
					call_user_func_array(array($obj, $argv[2]), $funcargs);
				}
			}
		}
	}
}
