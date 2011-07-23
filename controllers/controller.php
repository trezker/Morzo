<?php

class Controller
{
	public function Load_model($model)
	{
		require_once "models/".strtolower($model).".php";
		$this->$model = new $model();
	}

	public function Load_controller($controller)
	{
		require_once "controllers/".strtolower($controller).".php";
		$this->$controller = new $controller();
	}
}

?>
