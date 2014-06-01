<?php

require_once '../framework/cache.php';

Mock::generate('Cache');

class TestOfController extends UnitTestCase {
    function test_constructor() {
		//Create dependency classes
		$model_factory = new Mock_Model_factory();
		$controller_factory = new Mock_Controller_factory();
		$session = new MockSession();
		$input = new MockInput();
		$cache = new MockCache();

		//Create controller
		$controller = new Controller($model_factory, $controller_factory, $session, $input, $cache);
		
		$this->assertTrue($controller !== NULL);
    }
}
