<?php
require_once "../controllers/controller.php";

class Front extends Controller
{
	public function Index()
	{
		if(isset($_SESSION['userid']))
		{
			header("Location: user");
			return;
		}

		$this->Load_view('front_view');
	}
	
	public function Get_login_view() {
		$this->Load_view('login_view');
	}
}
