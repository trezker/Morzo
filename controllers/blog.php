<?php
require_once "../controllers/controller.php";

class Blog extends Controller {
	public function Index() {
		//List latest blogpost titles
		//List Blogs
		$this->Load_model('Blog_model');
		$titles = $this->Blog_model->Get_latest_titles();
		//$posts = $this->Blog_model->Get_latest_posts();
		$blogs = $this->Blog_model->Get_blogs();

		$this->Load_view('blog_view', array(
											'posts' => $titles,
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
	
	private function Load_blog_control_panel($blog_name, $post_id = -1) {
		if(!$this->Blog_model->User_owns_blog_name($blog_name, $_SESSION['userid'])) {
			return 'Not your blog';
		}
		
		$blog = $this->Blog_model->Get_blog_by_name($blog_name);
		$titles = $this->Blog_model->Get_blog_post_titles($blog['ID']);
		if($post_id == -1) {
			$post = array(
						'ID' => -1,
						'Title' => '',
						'Content' => ''
					);
		}
		else {
			$post = $this->Blog_model->Get_blog_post($post_id);
		}
		
		return $this->Load_view('blog_control_panel_view', array(
																'blog' => $blog,
																'post' => $post,
																'titles' => $titles
																), true);
		//Load post from file
		//Write into form
		//List of existing posts
	}

	public function Edit_blogpost() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}

		$post_id = $_POST['post_id'];
		$this->Load_model('Blog_model');
		$blog_name = $this->Blog_model->Get_blog_name_from_post_id($post_id);
		$blog_control_panel_view = $this->Load_blog_control_panel($blog_name, $post_id);
		
		echo json_encode(array('success' => true, 'blog_control_panel_view' => $blog_control_panel_view));
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
	
	public function Submit_blog_post() {
		header('Content-type: application/json');
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			echo json_encode(array('success' => false, 'reason' => 'Not logged in'));
			return;
		}
		
		$blog_id = $_POST['blog_id'];
		$post_id = $_POST['post_id'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		
		$this->Load_model('Blog_model');
		$blog_name = $this->Blog_model->Get_blog_name_from_post_id($post_id);
		if(!$this->Blog_model->User_owns_blog_name($blog_name, $_SESSION['userid'])) {
			echo json_encode(array('success' => false, 'reason' => 'Not your blog'));
		}
		
		if($post_id == -1) {
			$r = $this->Blog_model->Create_blog_post($blog_id, $title, $content);
		} else {
			$r = $this->Blog_model->Update_blog_post($post_id, $title, $content);
		}
		
		echo json_encode($r);
	}
	
	public function View($blog_name) {
		$this->Load_model('Blog_model');
		$titles = $this->Blog_model->Get_latest_titles();
		$blogs = $this->Blog_model->Get_blogs();

		$this->Load_view('blog_view', array(
											'titles' => $titles,
											'blogs' => $blogs
											));
	}
}
