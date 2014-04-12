<?php
require_once "../controllers/base.php";

class Front extends Base
{
	public function Index()
	{
		if(isset($_SESSION['userid'])) {
			header("Location: user");
			return;
		}

		$this->Load_model('Blog_model');
		$posts = $this->Blog_model->Get_posts(1, 5, 0);
		$blogs = $this->Blog_model->Get_blogs();

		$this->Load_model('User_model');
		$openid_icons = $this->User_model->Get_openid_icons();

		return array(
			"type" => "view", 
			"view" => 'front_view', 
			"data" => array(
				'openid_icons' => $openid_icons,
				'posts' => $posts,
				'blogs' => $blogs,
				'show_owner_controls' => false
			)
		);
	}
	
	public function Get_login_view() {
		$this->Load_model('User_model');
		$openid_icons = $this->User_model->Get_openid_icons();
		$this->Load_view('login_view', array('openid_icons' => $openid_icons));
	}
}
