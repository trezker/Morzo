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
		$openid_icons = array(
			array(	'icon' => '/data/openid_icons/google.ico.png',
					'URI'	=> 'https://www.google.com/accounts/o8/id',
					'name'	=> 'Google'),
			array(	'icon' => '/data/openid_icons/myopenid.ico.png',
					'URI'	=> 'https://www.myopenid.com',
					'name'	=> 'myOpenID')
		);
		$this->Load_view('login_view', array('openid_icons' => $openid_icons));
	}
}
