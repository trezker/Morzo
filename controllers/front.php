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
			header("Location: user");
			return;
		}

		include 'views/front_view.php';
	}
	
	public function Get_login_view() {
		include 'views/login_view.php';
	}
}
?>
