<?php

require_once '../controllers/front.php';
require_once '../models/blog_model.php';
require_once '../models/user_model.php';
require_once '../controllers/user.php';

Mock::generate('Blog_model');
Mock::generate('User_model');

class Front_tests extends Controller_testbase {
    function test_Index() {
		$posts = array(
			array(
				"ID" => 1, 
				"Title" => "The title", 
				"Content" => "The content", 
				"Created_date" => "2014-04-26 00:00:00",
				"Blog_name" => "The blog", 
				"Blog_ID" => 1
			)
		);
		$this->model_factory->models["Blog_model"] = new MockBlog_model();
		$this->model_factory->models["Blog_model"]->returns('Get_posts', $posts, array(1, 5, 0));
		$this->model_factory->models["User_model"] = new MockUser_model();
		$this->controller_factory->controllers["User"] = new MockUser();

		$front = new Front($this->model_factory, $this->controller_factory, $this->session, $this->input, $this->cache);
		$response = $front->Index();
		$this->view_factory->Load_view($response["view"], $response["data"]);
		
		$this->assertTrue($response["view"] === "front_view");
		$this->assertTrue($response["data"]["posts"][0]["ID"] === 1);
	}
}
