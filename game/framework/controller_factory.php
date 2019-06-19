<?php

class Controller_factory {
	private $model_factory = null;
	private $session = null;
	private $input = null;
	private $cache = null;
	
	function __construct($model_factory, $session, $input, $cache) {
		$this->model_factory = $model_factory;
		$this->session = $session;
		$this->input = $input;
		$this->cache = $cache;
	}
	
	public function Load_controller($controller) {
		if(isset($this->$controller) === false) {
			require_once "../controllers/".strtolower($controller).".php";
			$this->$controller = new $controller($this->model_factory, $this, $this->session, $this->input, $this->cache);
		}
		return $this->$controller;
	}
}
