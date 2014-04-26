<?php

/* TODO:
 * Tests need to put global variables in a known state.
 * Session, post and get should perhaps be provided to the controller by index.php
 * 
 * Might it be a good solution to present a webpage with options for which tests to run.
 * Each test could be an ajax call, that would isolate them and each test can easily present its own result.
 */
require_once '../controllers/front.php';
require_once '../models/blog_model.php';
require_once '../models/user_model.php';

Mock::generate('Blog_model');
Mock::generate('User_model');

class TestOfFront extends UnitTestCase {
    function testIndex() {
		$posts = array(
			array(
				"ID" => 1, 
				"Title" => "The title", 
				"Content" => "The content", 
				"Created_date" => "2014-04-26",
				"Blog_name" => "The blog", 
				"Blog_ID" => 1
			)
		);
		$model_factory = new Mock_Model_factory();
		$model_factory->models["Blog_model"] = new MockBlog_model();
		$model_factory->models["Blog_model"]->returns('Get_posts', $posts, array(1, 5, 0));
		$model_factory->models["User_model"] = new MockUser_model();
		$controller_factory = new Mock_Controller_factory();

		$front = new Front($model_factory, $controller_factory);
		$response = $front->Index();
		$this->assertTrue($response["view"] == "front_view");
		$this->assertTrue($response["data"]["posts"][0]["ID"] == 1);
    }
}
