<?php

class Controller {
	private $model_factory = null;
	private $controller_factory = null;
	
	function __construct($model_factory, $controller_factory) {
		$this->model_factory = $model_factory;
		$this->controller_factory = $controller_factory;
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
		$result = Load_view($view, $data);
		if($return == true)
			return $result;
		else
			echo $result;
	}
}
