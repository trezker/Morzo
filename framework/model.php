<?php

class Model
{
	protected $db = null;
	
	function __construct($database) {
		$this->db = $database;
	}
	
	public function Load_model($model) {
		if(isset($this->$model))
			return;
		require_once "../models/".strtolower($model).".php";
		$this->$model = new $model($this->db);
	}
}


