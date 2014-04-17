<?php

class Controller_factory {
	private $model_factory = null;
	
	function __construct($model_factory) {
		$this->model_factory = $model_factory;
	}	
	
	public function Load_controller($controller) {
		if(isset($this->$controller) === false) {
			require_once "../controllers/".strtolower($controller).".php";
			$this->$controller = new $controller($this, $this->model_factory);
		}
		return $this->$controller;
	}
}
