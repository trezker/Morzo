<?php

/* TODO:
 * Tests need to put global variables in a known state.
 * Session, post and get should perhaps be provided to the controller by index.php
 */
require_once '../controllers/actor.php';

class Mock_MVCFactory {
	public $models = array();
	public $controllers = array();
	public $views = array();
	
	public function Load_model($model) {
	}
	
	public function Load_controller($controller) {
	}
	
	public function Load_view($view, $data = array(), $return = false) {
		//TODO: It's possible to create views that verify the data it gets instead of rendering a page...
		//Need another way if it's json output.
	}
}

class Actor_tests {
	public function Request_actor_not_logged_in() {

		$obj = new Actor($db);
	}
}
