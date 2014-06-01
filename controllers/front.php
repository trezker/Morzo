<?php
require_once "../controllers/base.php";

class Front extends Base {
	public function Index() {
		$this->Load_controller('User');
		if($this->User->Logged_in()) {
			return array(
				'view' => 'redirect',
				'data' => '/user'
			);
		}

		$this->Load_model('Blog_model');
		$posts = $this->Blog_model->Get_posts(1, 5, 0);
		$blogs = $this->Blog_model->Get_blogs();

		$this->Load_model('User_model');
		$openid_icons = $this->User_model->Get_openid_icons();

		return array(
			'view' => 'front_view',
			'data' => array(
				'openid_icons' => $openid_icons,
				'posts' => $posts,
				'blogs' => $blogs,
				'show_owner_controls' => false
			)
		);
	}
}
