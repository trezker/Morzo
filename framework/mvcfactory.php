<?php

class MVCFactory {
	private $db = null;
	
	function __construct($database) {
		$this->db = $database;
	}
	
	public function Load_model($model) {
		if(isset($this->$model) === false) {
			require_once "../models/".strtolower($model).".php";
			$this->$model = new $model($this->db);
		}
		return $this->$model;
	}

	public function Load_controller($controller) {
		if(isset($this->$controller) === false) {
			require_once "../controllers/".strtolower($controller).".php";
			$this->$controller = new $controller($this->db);
		}
		return $this->$controller;
	}

	public function Load_view($view, $data = array()) {
		foreach($data as $key => $value) {
			$$key = $value;
		}
		ob_start();
		include "../views/".strtolower($view).".php";
		$result = ob_get_clean();
		$this->Load_model("Language_model");
		$result = $this->Language_model->Translate_tokens($result);
		return $result;
	}
}
