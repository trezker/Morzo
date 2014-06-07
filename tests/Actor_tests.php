<?php

require_once '../controllers/actor.php';
require_once '../controllers/user.php';
require_once '../controllers/update.php';
require_once '../models/actor_model.php';
require_once '../models/travel_model.php';

Mock::generate('User');
Mock::generate('Update');
Mock::generate('Actor_model');
Mock::generate('Travel_model');

class TestOfActor extends Controller_testbase {
    function test_Precondition_not_logged_in_redirect() {
		//Set up mock resources
		$this->controller_factory->controllers["User"] = new MockUser();

		//Create controller
		$actor = new Actor($this->model_factory, $this->controller_factory, $this->session, $this->input, $this->cache);

		//Call function
		$response = $actor->Precondition(array(0));
		
		//Check result
		$this->assertTrue($response["view"] === "redirect");
		$this->assertTrue($response["data"] === "/");
    }

    function test_Precondition_pass() {
		//Set up mock resources
		$this->model_factory->models["Actor_model"] = new MockActor_model();
		$this->model_factory->models["Actor_model"]->returns('User_owns_actor', true);

		$this->controller_factory->controllers["User"] = new MockUser();
		$this->controller_factory->controllers["User"]->returns('Logged_in', true);

		//Create controller
		$actor = new Actor($this->model_factory, $this->controller_factory, $this->session, $this->input, $this->cache);

		//Call function
		$response = $actor->Precondition(array(0));
		
		//Check result
		$this->assertTrue($response === true);
    }

    function test_Show_actor_not_your_actor_redirect() {
		//Set up mock resources
		$this->model_factory->models["Actor_model"] = new MockActor_model();

		$this->controller_factory->controllers["User"] = new MockUser();
		$this->controller_factory->controllers["User"]->returns('Logged_in', true);

		//Create controller
		$actor = new Actor($this->model_factory, $this->controller_factory, $this->session, $this->input, $this->cache);
		
		//Call function
		$response = $actor->Precondition(array(0));
		
		//Check result
		$this->assertTrue($response["view"] === "redirect");
		$this->assertTrue($response["data"] === "/");
    }
}
