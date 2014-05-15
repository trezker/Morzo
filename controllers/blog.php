<?php
require_once "../controllers/base.php";

class Blog extends Base {
	function Precondition($args) {
		$header_accept = $this->Input_header("Accept");
		$json_request = false;
		if (strpos($header_accept,'application/json') !== false) {
			$json_request = true;
		}
		
		$this->Load_controller('User');
		if(!$this->User->Logged_in()) {
			if($json_request === true) {
				return $this->Json_response_not_logged_in();
			} else {
				return array("view" => "redirect", "data" => "/");
			}
		}
		return true;
	}

	public function Index() {
		//List latest blogpost titles
		//List Blogs
		$this->Load_model('Blog_model');
		$posts = $this->Blog_model->Get_posts();
		//$posts = $this->Blog_model->Get_latest_posts();
		$blogs = $this->Blog_model->Get_blogs();

		return array(
			'view' => 'blog_view',
			'data' => array(
				'posts' => $posts,
				'blogs' => $blogs,
				'blogposts_view' => array(
					'view' => 'blogposts_view',
					'data' => array(
						'posts' => $posts
					)
				)
			)
		);
	}
	
	public function Control_panel($blog_name = null, $post_id = -1) {
		$this->Load_model('Blog_model');
		$blogs = $this->Blog_model->Get_user_blogs($this->Session_get('userid'));
		$blog_control_panel_view = "";
		if($blog_name) {
			if(!$this->Blog_model->User_owns_blog_name($blog_name, $this->Session_get('userid'))) {
				return array(
					'view' => 'redirect',
					'data' => '/'
				);
			}
			$blog_control_panel_view = $this->Load_blog_control_panel($blog_name, $post_id);
		}
		
		return array(
			'view' => 'blog_control_panel_main_view',
			'data' => array(
				'blogs' => $blogs,
				'blog_control_panel_view' => $blog_control_panel_view
			)
		);
	}
	
	private function Load_blog_control_panel($blog_name, $post_id = -1) {
		$blog = $this->Blog_model->Get_blog_by_name($blog_name);
		$titles = $this->Blog_model->Get_blog_post_titles($blog['ID']);
		if($post_id == -1) {
			$post = array(
						'ID' => -1,
						'Title' => '',
						'Content' => '',
						'Hidden' => '0'
					);
		}
		else {
			$post = $this->Blog_model->Get_blog_post($post_id);
		}
		
		return array(
			'view' => 'blog_control_panel_view',
			'data' => array(
				'blog_name' => $blog_name,
				'blog' => $blog,
				'post' => $post,
				'titles' => $titles
			)
		);
	}

	public function Create_blog() {
		$this->Load_model('Blog_model');
		$r = $this->Blog_model->Create_blog($this->Session_get('userid'), $this->Input_post('name'));
		
		echo array(
			'view' => 'data_json',
			'data' => $r
		);
	}
	
	public function Submit_blog_post() {
		$blog_id = $this->Input_post('blog_id');
		$post_id = $this->Input_post('post_id');
		$title = $this->Input_post('title');
		$content = $this->Input_post('content');
		$hidden = $this->Input_post('hidden')=="true"?1:0;
		
		$this->Load_model('Blog_model');
		if($post_id != -1) {
			$blog_id = $this->Blog_model->Get_blog_from_post_id($post_id);
		}

		if(!$this->Blog_model->User_owns_blog($blog_id, $this->Session_get('userid'))) {
			return array(
				'view' => 'data_json',
				'data' => array(
					'success' => false,
					'reason' => 'Not your blog'
				)
			);
		}
		
		if($post_id == -1) {
			$r = $this->Blog_model->Create_blog_post($blog_id, $title, $content, $hidden);
		} else {
			$r = $this->Blog_model->Update_blog_post($post_id, $title, $content, $hidden);
		}
		
		echo array(
			'view' => 'data_json',
			'data' => $r
		);
	}

	public function Delete_blogpost() {
		$post_id = $this->Input_post('post_id');
		
		$this->Load_model('Blog_model');
		if($post_id != -1) {
			$blog_id = $this->Blog_model->Get_blog_from_post_id($post_id);
		}
		if(!$this->Blog_model->User_owns_blog($blog_id, $this->Session_get('userid'))) {
			return array(
				'view' => 'data_json',
				'data' => array(
					'success' => false,
					'reason' => 'Not your blog'
				)
			);
		}
		
		$r = $this->Blog_model->Delete_blogpost($post_id);
		
		echo array(
			'view' => 'data_json',
			'data' => $r
		);
	}

	public function Hide_blogpost() {
		$post_id = $this->Input_post('post_id');
		
		$this->Load_model('Blog_model');
		if($post_id != -1) {
			$blog_id = $this->Blog_model->Get_blog_from_post_id($post_id);
		}
		if(!$this->Blog_model->User_owns_blog($blog_id, $this->Session_get('userid'))) {
			return array(
				'view' => 'data_json',
				'data' => array(
					'success' => false,
					'reason' => 'Not your blog'
				)
			);
		}
		
		$r = $this->Blog_model->Hide_blogpost($post_id);
		echo array(
			'view' => 'data_json',
			'data' => $r
		);
	}
	
	public function View($blog_name) {
		$show_owner_controls = false;
		$this->Load_model('Blog_model');
		if($this->Blog_model->User_owns_blog_name($blog_name, $this->Session_get('userid'))) {
			$show_owner_controls = true;
		}
		$posts = $this->Blog_model->Get_posts();
		$blogs = $this->Blog_model->Get_blogs();

		return array(
			'view' => 'blog_view',
			'data' => array(
				'posts' => $posts,
				'blogs' => $blogs,
				'blogposts_view' => array(
					'view' => 'blogposts_view',
					'data' => array(
						'posts' => $posts,
						'blogs' => $blogs,
						'show_owner_controls' => $show_owner_controls
					)
				)
			)
		);
	}
	
	public function Preview_post() {
		$this->Load_controller('User');
		$show_owner_controls = false;
		$this->Load_model('Blog_model');
		
		$blog_id = $_POST['blog_id'];
		$post_id = $_POST['post_id'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		
		if($this->User->Logged_in()) {
			/*
			if($this->Blog_model->User_owns_blog_name($blog_name, $_SESSION['userid'])) {
				$show_owner_controls = true;
			}*/
		}
		
		$posts = array(
					array(
						'Title' => $title,
						'Content' => $content,
						'Created_date' => '1111-11-11 11:11:11',
						'Blog_name' => ""
						)
					);
		
		$blogposts_view = $this->Load_view('blogposts_view', array(
											'posts' => $posts,
											'blogs' => null,
											'show_owner_controls' => false
											), true);

		echo json_encode(array('success' => true, 'data' => $blogposts_view));
	}
}
