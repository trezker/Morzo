<?php

class Front
{
	function __construct()
	{
	}

	function __destruct()
	{
	}

	public function Index()
	{
		if(isset($_SESSION['userid']))
		{
			include 'views/user_view.php';
			return;
		}

		include 'views/front_view.php';
	}
}
?>
