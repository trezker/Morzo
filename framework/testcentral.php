<?php

class Testcentral {
	private function get_public_methods($classname) {
		$result = array();
		foreach (get_class_methods($classname) as $method) {
			$reflect = new ReflectionMethod($classname, $method);
			if($reflect->isPublic()) {
				array_push($result, $method);
			}
		}
		return $result;
	}

	private function get_tests() {
		$tests = array();
		if ($handle = opendir('../tests')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$test_path = '../tests/'.$entry;
					include($test_path);
					$ext = pathinfo($entry, PATHINFO_EXTENSION);
					$classname = basename($entry, ".".$ext);
					$tests[$classname] = array();
					$methods = $this->get_public_methods($classname);
					foreach ($methods as $method) {
						array_push($tests[$classname], $method);
					}
				}
			}
			closedir($handle);
		}
		return $tests;
	}

	public function Show_testpage() {
		echo '
			<html>
				<head>
					<style>
						.result-pass {
							background-color: green;
						}
						.result-fail {
							background-color: red;
						}
					</style>
					<script type="text/javascript" src="/js/moment-with-langs.min.js">	</script>
					<script type="text/javascript" src="/js/jquery-1.10.2.min.js">	</script>					
					<script type="text/javascript" src="/js/testcentral.js"></script>
				</head>
				<body>
					<h1>Testing central</h1>
					<fieldset>
						<legend>All tests</legend>
						<a href="javascript:void(0)" onclick="test_all()">Run</a>
						<a href="javascript:void(0)" onclick="clear_all()">Clear</a>
					</fieldset>
			 ';
		$tests = $this->get_tests();
		foreach ($tests as $classname => $methods) {
			echo '<fieldset data-suite="' . $classname . '" class="test-suite">
					<legend><a href="javascript:void(0)" class="test-suite-link">Run</a> ' . $classname . '</legend>
				 ';
			foreach ($methods as $method) {
				echo '
					<div data-method="' . $method . '" class="test-method">
						<a href="javascript:void(0)" class="test-method-link">Run</a> ' . $method . '
					</div>
					';
			}
			echo '</fieldset>';
		}
		echo '
				</body>
			</html>
			 ';
	}
	
	public function Run_test() {
		$suite = $_POST["suite"];
		$method = $_POST["method"];
		
		require_once("../tests/" . $suite . ".php");
		$test = new $suite();
		
		try {
			ob_start();
			$test->$method();
			$info = ob_get_clean();
		}
		catch(Exception $e) {
			$info = "Exception: " . $e->getMessage();
		}

		header('Content-type: application/json');
		$result = array(
			'success' => true,
			'info' => $info
		);
		echo json_encode($result);
	}
}
