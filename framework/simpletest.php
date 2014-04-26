<?php
require_once('../libraries/simpletest/autorun.php');

class Mock_Model_factory {
	public $models = array();
	
	public function Load_model($model) {
		return $this->models[$model];
	}	
}

class Mock_Controller_factory {
	public $controllers = array();
	
	public function Load_controller($controller) {
		return $controllers[$controller];
	}	
}

//Include all the test files
if ($handle = opendir('../tests')) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
			$test_path = '../tests/'.$entry;
			require_once($test_path);
		}
	}
	closedir($handle);
}
