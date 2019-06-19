<?php

Mock::generate('Session');
Mock::generate('Input');
Mock::generate('Cache');

class Controller_testbase extends UnitTestCase {
	protected $model_factory;
	protected $view_factory;
	protected $controller_factory;
	protected $session;
	protected $input;
	protected $cache;

    function setUp() {
		$this->model_factory = new Mock_Model_factory();
		$this->view_factory = new Mock_View_factory();
		$this->controller_factory = new Mock_Controller_factory();
		$this->session = new MockSession();
		$this->input = new MockInput();
		$this->cache = new MockCache();
	}
	
    function test_constructor() {
		$controller = new Controller($this->model_factory, $this->controller_factory, $this->session, $this->input, $this->cache);
		$this->assertTrue($controller !== NULL);
    }
}
