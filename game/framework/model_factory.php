<?php

class Model_factory {
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
}
