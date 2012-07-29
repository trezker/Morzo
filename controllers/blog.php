<?php
require_once "../controllers/controller.php";

class Blog extends Controller {
	public function Index() {
		//List latest blogpost titles
		//List Blogs
		$this->Load_model('Blog_model');
		$titles = $this->Blog_model->Get_latest_titles();
		$blogs = $this->Blog_model->Get_blogs();

		$this->Load_view('blog_view', array(
											'titles' => $titles,
											'blogs' => $blogs
											));
	}
	
	public function Control_panel($blog_name = null) {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			header("Location: /front");
			exit;
		}

		$this->Load_model('Blog_model');
		$blogs = $this->Blog_model->Get_user_blogs($_SESSION['userid']);
		
		$blog_control_panel_view = "";
		if($blog_name) {
			$blog_control_panel_view = $this->Load_blog_control_panel($blog_name);
		}
		
		$this->Load_view('blog_control_panel_main_view', array(
													'blogs' => $blogs,
													'blog_control_panel_view' => $blog_control_panel_view)
													);
		//Create/Delete/Rename
		//Get blog specific control panel through ajax Load_blog_control_panel
	}
	
	private function Load_blog_control_panel($blog_name) {
		if(!$this->Blog_model->User_owns_blog_name($blog_name, $_SESSION['userid'])) {
			return 'Not your blog';
		}
		
		$blog = $this->Blog_model->Get_blog_by_name($blog_name);
		
		return $this->Load_view('blog_control_panel_view', array('blog' => $blog), true);
		//Load post from file
		//Write into form
		//List of existing posts
	}
	
	public function Create_blog() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		
		$this->Load_model('Blog_model');
		$r = $this->Blog_model->Create_blog($_SESSION['userid'], $_POST['name']);
		
		echo json_encode($r);
	}
	
	public function Create_blog_post() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		
		$blog_id = $_POST['blog_id'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		
		$this->Load_model('Blog_model');
		if(!$this->Blog_model->User_owns_blog($blog_id, $_SESSION['userid'])) {
			echo json_encode(array('success' => false, 'reason' => 'Not your blog'));
		}
		
		$r = $this->Blog_model->Create_blog_post($blog_id, $title, $content);
		
		echo json_encode($r);
	}
}


