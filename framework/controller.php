<?php

class Controller {
	private $model_factory = null;
	private $controller_factory = null;
	private $session = null;
	private $input = null;
	protected $cache = null;
	
	function __construct($model_factory, $controller_factory, $session, $input, $cache) {
		$this->model_factory = $model_factory;
		$this->controller_factory = $controller_factory;
		$this->session = $session;
		$this->input = $input;
		$this->cache = $cache;
	}
	
	function Session_get($key) {
		return $this->session->Get($key);
	}

	function Session_set($key, $value) {
		return $this->session->Set($key, $value);
	}
	
	function Session_clear() {
		$this->session->Clear();
	}
	
	function Input_get($key) {
		return $this->input->Get($key);
	}

	function Input_post($key = NULL) {
		return $this->input->Get_post($key);
	}

	function Input_cookie($key) {
		return $this->input->Get_cookie($key);
	}
	
	function Show_headers() {
		$this->input->Show_headers();
	}
	
	function Input_header($key) {
		return $this->input->Get_header($key);
	}

	function Input_global($key) {
		return $this->input->Get_global($key);
	}

	function Model_factory() {
		return $this->model_factory;
	}

	function Controller_factory() {
		return $this->controller_factory;
	}
	
	public function Load_model($model) {
		$this->$model = $this->model_factory->Load_model($model);
	}

	public function Load_controller($controller) {
		$this->$controller = $this->controller_factory->Load_controller($controller);
	}
}
