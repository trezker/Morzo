<?php

class Controller
{
	public function Load_model($model)
	{
		if(isset($this->$model))
			return;
		require_once "../models/".strtolower($model).".php";
		$this->$model = new $model();
	}

	public function Load_controller($controller)
	{
		if(isset($this->$controller))
			return;
		require_once "../controllers/".strtolower($controller).".php";
		$this->$controller = new $controller();
	}
}


