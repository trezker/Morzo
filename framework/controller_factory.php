<?php

class Controller_factory {
	private $model_factory = null;
	private $session = null;
	
	function __construct($model_factory, $session) {
		$this->model_factory = $model_factory;
		$this->session = $session;
	}
	
	public function Load_controller($controller) {
		if(isset($this->$controller) === false) {
			require_once "../controllers/".strtolower($controller).".php";
			$this->$controller = new $controller($this, $this->model_factory, $this->session);
		}
		return $this->$controller;
	}
}
