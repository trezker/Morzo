<?php

/* TODO:
 * Tests need to put global variables in a known state.
 * Session, post and get should perhaps be provided to the controller by index.php
 * 
 * Might it be a good solution to present a webpage with options for which tests to run.
 * Each test could be an ajax call, that would isolate them and each test can easily present its own result.
 */
require_once '../controllers/actor.php';
require_once '../controllers/user.php';

Mock::generate('User');

class TestOfActor extends UnitTestCase {
    function test_Show_actor_redirect_not_logged_in() {
		$model_factory = new Mock_Model_factory();

		$controller_factory = new Mock_Controller_factory();
		$controller_factory->controllers["User"] = new MockUser();

		$actor = new Actor($model_factory, $controller_factory);
		$response = $actor->Show_actor(0, '');
		
		$this->assertTrue($response["type"] === "redirect");
		$this->assertTrue($response["data"] === "/");
    }
}
