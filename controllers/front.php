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

		$this->Load_model('Blog_model');
		$posts = $this->Blog_model->Get_posts(1, 5, 0);
		$blogs = $this->Blog_model->Get_blogs();

		$blogposts_view = $this->Load_view('blogposts_view', array(
											'posts' => $posts,
											'blogs' => $blogs,
											'show_owner_controls' => false
											), true);

		$this->Load_model('User_model');
		$openid_icons = $this->User_model->Get_openid_icons();

		$this->Load_view('front_view', array(
											'blogposts_view' => $blogposts_view,
											'openid_icons' => $openid_icons
											));
	}
	
	public function Get_login_view() {
		$this->Load_model('User_model');
		$openid_icons = $this->User_model->Get_openid_icons();
		$this->Load_view('login_view', array('openid_icons' => $openid_icons));
	}
}
