<?php

class Controller {
	private $model_factory = null;
	private $controller_factory = null;
	private $session = null;
	private $input = null;
	
	function __construct($model_factory, $controller_factory, $session, $input) {
		$this->model_factory = $model_factory;
		$this->controller_factory = $controller_factory;
		$this->session = $session;
		$this->input = $input;
	}
	
	function Session_get($key) {
		return $this->session->Get($key);
	}

	function Session_set($key, $value) {
		return $this->session->Set($key, $value);
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

	public function Load_view($view, $data = array(), $return = false) {
		//TODO: Eliminate all use of this function.
		foreach($data as $key => $value) {
			$$key = $value;
		}
		ob_start();
		include "../views/".strtolower($view).".php";
		$result = ob_get_clean();
		//Make a view factory instead that uses the model factory.
		$language_model = $this->model_factory->Load_model("Language_model");
		$result = $language_model->Translate_tokens($result);
		if($return == true)
			return $result;
		else
			echo $result;
	}
}
