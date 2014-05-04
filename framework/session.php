<?php

class Session {
	function __construct() {
		session_start();
	}

	public function Set($key, $value) {
		$_SESSION[$key] = $value;
	}
	
	public function Get($key) {
		if(isset($_SESSION[$key]))
			return $_SESSION[$key];
		return NULL;
	}
}
