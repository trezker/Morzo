<?php

/* 1. parse the URL */
$argv = explode("/",$_SERVER['PATH_INFO']);

/* 2 security check */
//    [omitted for the sake of simplicity]

/* 3 populate the page with uniqid's content */
//    [omitted for the sake of simplicity]

if($argv[1] == "info")
{
	phpinfo();
	if(count($argv)>1)
	{
		require_once('controllers/'.$argv.'.php');
	}
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
		$controller_path = 'controllers/'.$argv[1].'.php';
		if(!file_exists($controller_path))
		{
			include 'blocked.php';
		}
		else
		{
			$r = include($controller_path);
	//		echo 'Result: '. $r .' : ' . $controller_path . '<br />';
			if ($r != 1) {
				//Todo: Log error
	//			echo 'Failed '.$r.': ' . $controller_path . '<br />';
				include 'blocked.php';
			}
			else
			{
				$obj = new $argv[1];

				if(count($argv)<3)
				{
					$obj->Index();
				}
				else if(!method_exists($obj, $argv[2]))
				{
					echo 'You\'re not supposed to be here <br />';
				}
				else
				{
					$funcargs = array_slice($argv, 3);
					call_user_func_array(array($obj, $argv[2]), $funcargs);
				}
			}
		}
	}
}
?>
