<?php

class Model
{
	public function Load_model($model) {
		if(isset($this->$model))
			return;
		require_once "../models/".strtolower($model).".php";
		$this->$model = new $model();
	}
}


