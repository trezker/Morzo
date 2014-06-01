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
require_once '../controllers/update.php';
require_once '../models/actor_model.php';
require_once '../models/travel_model.php';

Mock::generate('Session');
Mock::generate('Input');
Mock::generate('User');
Mock::generate('Update');
Mock::generate('Actor_model');
Mock::generate('Travel_model');

/*
 * We have to call precheck before the controller function.
 * Think through this...
 * We don't want to write tests for prechecks on every method.
 * The precheck behaviours should be tested separately.
 * */
class TestOfActor extends UnitTestCase {
    function test_Precondition_not_logged_in_redirect() {
		//Create dependency classes
		$model_factory = new Mock_Model_factory();
		$controller_factory = new Mock_Controller_factory();
		$session = new MockSession();
		$input = new MockInput();
		$cache = new MockCache();

		//Set up mock resources
		$controller_factory->controllers["User"] = new MockUser();

		//Create controller
		$actor = new Actor($model_factory, $controller_factory, $session, $input, $cache);

		//Call function
		$response = $actor->Precondition(array(0));
		
		//Check result
		$this->assertTrue($response["view"] === "redirect");
		$this->assertTrue($response["data"] === "/");
    }

    function test_Precondition_pass() {
		//Create dependency classes
		$model_factory = new Mock_Model_factory();
		$controller_factory = new Mock_Controller_factory();
		$session = new MockSession();
		$input = new MockInput();
		$cache = new MockCache();

		//Set up mock resources
		$model_factory->models["Actor_model"] = new MockActor_model();
		$model_factory->models["Actor_model"]->returns('User_owns_actor', true);

		$controller_factory->controllers["User"] = new MockUser();
		$controller_factory->controllers["User"]->returns('Logged_in', true);

		//Create controller
		$actor = new Actor($model_factory, $controller_factory, $session, $input, $cache);

		//Call function
		$response = $actor->Precondition(array(0));
		
		//Check result
		$this->assertTrue($response === true);
    }

    function test_Show_actor_not_your_actor_redirect() {
		//Create dependency classes
		$model_factory = new Mock_Model_factory();
		$controller_factory = new Mock_Controller_factory();
		$session = new MockSession();
		$input = new MockInput();
		$cache = new MockCache();

		//Set up mock resources
		$model_factory->models["Actor_model"] = new MockActor_model();

		$controller_factory->controllers["User"] = new MockUser();
		$controller_factory->controllers["User"]->returns('Logged_in', true);

		//Create controller
		$actor = new Actor($model_factory, $controller_factory, $session, $input, $cache);
		
		//Call function
		$response = $actor->Precondition(array(0));
		
		//Check result
		$this->assertTrue($response["view"] === "redirect");
		$this->assertTrue($response["data"] === "/");
    }
}
