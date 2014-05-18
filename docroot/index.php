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
require_once '../util/database.php';
require_once '../framework/session.php';
require_once '../framework/input.php';
require_once '../framework/model_factory.php';
require_once '../framework/controller_factory.php';
require_once '../config.php';

if (empty($_SERVER['PATH_INFO'])) {
	$path = explode('?', $_SERVER['REQUEST_URI'], 2);
	$_SERVER['PATH_INFO'] = reset($path);
}
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
elseif($argv[1] == "test") {
	require_once "../framework/simpletest.php";
}
else
{
	//Load requested controller
	if(count($argv)>1)
	{
		$allowed_controllers = glob('../controllers/*.php');
		if($argv[1] == "") {
			$controller_name = "front";
		} else {
			$controller_name = $argv[1];
		}
		$controller_path = '../controllers/'.$controller_name.'.php';
		if(!in_array($controller_path, $allowed_controllers) || !file_exists($controller_path))
		{
			Log_message("Could not load controller: ".$controller_path);
			header("HTTP/1.0 404 Not Found");
			include '../blocked.php';
			exit;
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
				exit;
			}
			else
			{
				$db = Create_database_connection($config['database']['default']);
				$session = new Session();
				$input = new Input();
				$model_factory = new Model_factory($db);
				$controller_factory = new Controller_factory($model_factory, $session, $input);
				$obj = $controller_factory->Load_controller($controller_name);

				$response = null;
				if(count($argv) < 3) {
					$argv[] = 'Index';
				}
				
				if(!method_exists($obj, $argv[2])) {
					header("HTTP/1.0 404 Not Found");
					include '../blocked.php';
					exit;
				}
				else {
					$funcargs = array_slice($argv, 3);
					if(method_exists($obj, 'Before_page_load')) {
						call_user_func_array(array($obj, 'Before_page_load'), array());
					}

					if(method_exists($obj, 'Precondition')) {
						$response = call_user_func_array(array($obj, 'Precondition'), array($funcargs));
					} else {
						$response = true;
					}
					//Only call the function if preconditions have passed.
					if($response === true) {
						$response = call_user_func_array(array($obj, $argv[2]), $funcargs);
					}
				}
				if(is_array($response) === true) {
					require_once "../framework/view_factory.php";
					$view_factory = new View_factory($model_factory);
					echo $view_factory->Load_view($response["view"], $response["data"], true);
				}
			}
		}
	}
}
