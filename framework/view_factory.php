<?php

class View_factory {
	private $model_factory = null;
	private $session = null;
	
	function __construct($model_factory) {
		$this->model_factory = $model_factory;
	}
	
	function Load_view($view, $data = array(), $translate_tokens = false) {
		$view_factory = $this;
		/*
		foreach($data as $key => $value) {
			$$key = $value;
		}
		*/
		ob_start();
		include "../views/".strtolower($view).".php";
		$result = ob_get_clean();
		//Make a view factory instead that uses the model factory.
		if($translate_tokens === true) {
			$language_model = $this->model_factory->Load_model("Language_model");
			$result = $language_model->Translate_tokens($result);
		}
		return $result;
	}
}
