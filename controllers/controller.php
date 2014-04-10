<?php

class Controller {
	private $mvcfactory = null;
	
	function __construct($mvcfactory) {
		$this->mvcfactory = $mvcfactory;
	}
	
	public function Load_model($model) {
		$this->$model = $this->mvcfactory->Load_model($model);
	}

	public function Load_controller($controller) {
		$this->$controller = $this->mvcfactory->Load_controller($controller);
	}

	public function Load_view($view, $data = array(), $return = false) {
		$result = $this->mvcfactory->Load_view($view, $data);
		if($return == true)
			return $result;
		else
			echo $result;
	}
}
