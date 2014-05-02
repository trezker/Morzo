<?php

class Input {
	private $get = array();
	private $post = array();
	private $cookie = array();
	
	function __construct() {
		$this->get = $_GET;
		$this->post = $_POST;
		$this->cookie = $_COOKIE;
	}

	public function Get($key) {
		return $this->get[$key];
	}
	
	public function Get_post($key) {
		return $this->post[$key];
	}

	public function Get_cookie($key) {
		return $this->cookie[$key];
	}
}
