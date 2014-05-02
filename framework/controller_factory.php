<?php

class Controller_factory {
	private $model_factory = null;
	private $session = null;
	private $input = null;
	
	function __construct($model_factory, $session, $input) {
		$this->model_factory = $model_factory;
		$this->session = $session;
		$this->input = $input;
	}
	
	public function Load_controller($controller) {
		if(isset($this->$controller) === false) {
			require_once "../controllers/".strtolower($controller).".php";
			$this->$controller = new $controller($this->model_factory, $this, $this->session, $this->input);
		}
		return $this->$controller;
	}
}
