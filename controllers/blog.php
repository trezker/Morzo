<?php
require_once "../controllers/controller.php";

class Blog extends Controller {
	public function Index() {
		//List latest blogpost titles
		//List Blogs
		$this->Load_model('Blog_model');
		$posts = $this->Blog_model->Get_posts();
		//$posts = $this->Blog_model->Get_latest_posts();
		$blogs = $this->Blog_model->Get_blogs();

		$blogposts_view = $this->Load_view('blogposts_view', array(
											'posts' => $posts
											), true);

		$this->Load_view('blog_view', array(
											'posts' => $posts,
											'blogs' => $blogs,
											'blogposts_view' => $blogposts_view
											));
	}
	
	public function Control_panel($blog_name = null, $post_id = -1) {
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			header("Location: /front");
			exit;
		}

		$this->Load_model('Blog_model');
		$blogs = $this->Blog_model->Get_user_blogs($_SESSION['userid']);
		
		$blog_control_panel_view = "";
		if($blog_name) {
			$blog_control_panel_view = $this->Load_blog_control_panel($blog_name, $post_id);
		}
		
		$this->Load_view('blog_control_panel_main_view', array(
													'blogs' => $blogs,
													'blog_control_panel_view' => $blog_control_panel_view)
													);
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
		$blog_id = $this->Blog_model->Get_blog_from_post_id($post_id);
		if(!$this->Blog_model->User_owns_blog($blog_id, $_SESSION['userid'])) {
			echo json_encode(array('success' => false, 'reason' => 'Not your blog'));
		}
		$blog = $this->Blog_model->Get_blog($blog_id);
		$blog_control_panel_view = $this->Load_blog_control_panel($blog['Name'], $post_id);
		
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
		if($post_id != -1) {
			$blog_id = $this->Blog_model->Get_blog_from_post_id($post_id);
		}
		if(!$this->Blog_model->User_owns_blog($blog_id, $_SESSION['userid'])) {
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
		$this->Load_controller('User');
		$show_owner_controls = false;
		$this->Load_model('Blog_model');
		if($this->User->Logged_in()) {
			if($this->Blog_model->User_owns_blog_name($blog_name, $_SESSION['userid'])) {
				$show_owner_controls = true;
			}
		}
		$posts = $this->Blog_model->Get_posts();
		$blogs = $this->Blog_model->Get_blogs();

		$blogposts_view = $this->Load_view('blogposts_view', array(
											'posts' => $posts,
											'blogs' => $blogs,
											'show_owner_controls' => $show_owner_controls
											), true);

		$this->Load_view('blog_view', array(
											'posts' => $posts,
											'blogs' => $blogs,
											'blogposts_view' => $blogposts_view
											));
	}
}
