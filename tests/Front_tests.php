<?php

/* TODO:
 * Tests need to put global variables in a known state.
 * Session, post and get should perhaps be provided to the controller by index.php
 * 
 * Might it be a good solution to present a webpage with options for which tests to run.
 * Each test could be an ajax call, that would isolate them and each test can easily present its own result.
 */
require_once '../controllers/front.php';

class Mock_Model_factory {
	public $models = array();
	
	public function Load_model($model) {
	}	
}

class Mock_Controller_factory {
	public $controllers = array();
	
	public function Load_controller($controller) {
		return $controllers[$controller];
	}	
}

class Front_tests {
	public function Index() {
		$model_factory = new Mock_Model_factory();
		$controller_factory = new Mock_Controller_factory();
		$front = new Front($model_factory, $controller_factory);
		$response = $front->Index();
		
	}
}
